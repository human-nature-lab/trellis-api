<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use App\Models\Snapshot;
use App\Library\FileHelper;
use Error;
use Illuminate\Support\Collection;
use ZipArchive;
use App\Console\Commands\BaseCommand;
use App\Library\Hook;
use App\Models\Upload;
use App\Services\HookService;
use App\Services\SnapshotService;
use Exception;
use Illuminate\Support\Facades\Log;

class StudySnapshot extends BaseCommand {

  protected $signature = 'trellis:study:snapshot
    {--chunk-size=2000}
    {--study=}
    {--quick-check}
    {--skip-foreign}
    {--no-indices}
    {--force}
    {--no-completed-data}';

  protected $description = 'Take all of the data related to a study and put it into a sqlite database for syncing';

  private $schemaFile = '';
  private $indexFile = '';

  private $sqliteConn;
  private $mainConn;
  private $snapshotService;
  private $ignoredTables = [];
  private $specialTables = ['config'];
  private $surveyTables = ['datum', 'question_datum', 'action', 'survey_condition_tag', 'section_condition_tag'];

  public function handle(SnapshotService $ss) {
    $this->snapshotService = $ss;
    set_time_limit(0);
    app()->configure('temp');
    app()->configure('snapshot');
    $this->schemaFile = config('snapshot.sqliteSchema');
    $this->indexFile = config('snapshot.sqliteIndex');
    $this->ignoredTables = config('snapshot.ignoredTables');

    try {
      $this->time('pre-hooks', function () {
        return $this->runPreHooks();
      });
    } catch (Exception $e) {
      Log::error($e);
      return 1;
    } 
    
    $this->time('snapshot', function () {
      $this->runIt();
    });

    try {
      $this->time('post-hooks', function () {
        return $this->runPostHooks();
      });
    } catch (Exception $e) {
      Log::error($e);
      return 1;
    }  
  }

  private function runPreHooks () {
    $hookService = new HookService();
    $files = $hookService->getPreSnapshotHooks();
    Log::info("running pre-snapshot hooks:", $files);
    foreach ($files as $file) {
      $hook = new Hook($file);
      $code = $hook->run();
      if ($code !== 0) {
        return $code;
      }
    }
    return 0;
  }

  private function runPostHooks () {
    $hookService = new HookService();
    $files = $hookService->getPostSnapshotHooks();
    Log::info("running post-snapshot hooks:", $files);
    foreach ($files as $file) {
      $hook = new Hook($file);
      $code = $hook->run();
      if ($code !== 0) {
        return $code;
      }
    }
    return 0;
  }

  private function runIt () {
    $sqliteLocation = config('database.connections.snapshot.database');

    if (!$this->option('force')) {
      $isOutdated = $this->time('checking for database changes', function () {
        return $this->snapshotService->snapshotIsOutdated();
      });
  
      if ($isOutdated) {
        $this->info('A new snapshot needs to be created!');
      } else {
        $this->info('The latest snapshot matches current database');
        return;
      }
    }

    $this->time('cleaning old snapshots', function () {
      $this->cleanOldSnapshots();
    });

    $this->time('sqlite copy', function () use ($sqliteLocation) {
      $this->copyIntoSqlite($sqliteLocation);
    });

    $snapshotId = Uuid::uuid4();
    $temp = FileHelper::storagePath(config('temp.directory.path'));
    $snapshotName = "$snapshotId.sqlite.zip";
    $tempLocation = "$temp/$snapshotName";
    $finalLocation = FileHelper::storagePath(config('snapshot.directory.path')) . "/$snapshotName";
    
    // Zip sqlite database into temp
    $this->time('zip', function () use ($sqliteLocation, $tempLocation, $snapshotId) {
      return $this->zipDb($sqliteLocation, $tempLocation, (string)$snapshotId);
    });
    
    // Move into snapshot directory
    rename($tempLocation, $finalLocation);

    // Insert snapshot into the database
    $snapshotModel = new Snapshot();
    $snapshotModel->id = $snapshotId;
    $snapshotModel->file_name = $snapshotName;
    $md5Hash = hash_file("md5", $finalLocation);
    $this->info("calculated MD5 hash: $md5Hash");
    $snapshotModel->hash = $md5Hash;
    $snapshotModel->save();
  }

  private function cleanOldSnapshots () {
    $twoWeeksMS = 14 * 24 * 60 * 60;
    $removed = FileHelper::removeOldFiles(FileHelper::storagePath(config('snapshot.directory.path')), $twoWeeksMS);
    $c = count($removed);
    $this->info("Removed $c old snapshots");
  }

  private function zipDb (string $sqliteLocation, string $tempLocation, string $snapshotId) {
    $zip = new ZipArchive();
    if (!$zip->open($tempLocation, ZipArchive::CREATE)) {
      throw new Error("Unable to create zip archive at $tempLocation");
    }
    $zip->addFile($sqliteLocation, 'snapshot.db');
    $zip->close();
  }

  private function dataHasChanged (): bool {
    if ($this->option('force')) {
      return true;
    }
    $hasChanged = false;
    // First check if there has been an upload since the last snapshot creation
    $latestSnapshot = Snapshot::where('deleted_at',null)
        ->orderBy('created_at', 'desc')
        ->first();

    $this->info("latestSnapshot: " . $latestSnapshot['created_at']);

    $latestUpload = Upload::where('deleted_at', null)
        ->where('status', 'SUCCESS')
        ->orderBy('updated_at', 'desc')
        ->first();
    $this->info("latestUpload: " . $latestUpload['created_at']);

    if ($latestSnapshot == null) {
        // Always create a new snapshot if the snapshot table is empty
        $hasChanged = true;
        $this->info("No snapshot found, always create snapshot.");
    } else if ($latestUpload != null) {
        // Otherwise, create a snapshot if there is a newer upload than the latest snapshot
        $this->info("unix timestamp of latestSnapshot: " . strtotime($latestSnapshot['created_at']));
        $this->info("unix timestamp of latestUpload: " . strtotime($latestUpload['created_at']));
        $hasChanged = (strtotime($latestSnapshot['created_at']) < strtotime($latestUpload['created_at']));
        $this->info("latestSnapshot older than latestUpload?: " . (($hasChanged) ? "true" : "false"));
    }

    if (!$hasChanged) {
      $dbConnection = config('database.default');
      $databaseName = config("database.connections.$dbConnection.database");
      // Check the latest updated table that is synced
      $this->info("Checking the latest table update using the information_schema database.");
      $q = DB::table('information_schema.tables')->
        selectRaw('max(update_time) as latest_update_time')->
        where('table_schema', $databaseName)->
        whereNotIn('table_name', $this->ignoredTables);


      $this->info("latestUpdateQuery: " . $q->toSql());
      $latestUpdate = $q->first();
      $latestUpdateTime = $latestUpdate->latest_update_time;
      $this->info("unix timestamp of latestSnapshot: " . strtotime($latestSnapshot['created_at']));
      $this->info("unix timestamp of latestUpdate: " . strtotime($latestUpdate->latest_update_time));
      $snapshotCreationNeeded =  ( ($latestUpdateTime == null) || (strtotime($latestSnapshot['created_at']) < strtotime($latestUpdateTime)) );
      $this->info("latestSnapshot older than latestUpdate?: " . (($snapshotCreationNeeded) ? "true" : "false"));
    }
    
    return $hasChanged;
  }

  private function copyIntoSqlite (string $sqliteLocation) {
    $tables = [];

    // This can probably be changed on the fly to be a dynamic sqlite connection
    $this->time('Preparing database', function () use (&$tables, $sqliteLocation) {
      // This will probably fail if an existing connection is using the database.
      // I think that's the behavior we want though.
      if (file_exists($sqliteLocation)) {
        unlink($sqliteLocation);
      }
      $f = fopen($sqliteLocation, 'c+');
      fclose($f);
      $this->sqliteConn = DB::connection('snapshot');
      $this->mainConn = DB::connection();

      $tables = $this->getTables();

      // Create initial schema in the snapshot
      if (!$this->sqliteConn->unprepared(file_get_contents($this->schemaFile))) {
        throw new Error('Failed to created schema');
      };
    });

    $this->sqliteConn->statement('PRAGMA synchronous = OFF;');
    $this->sqliteConn->statement('PRAGMA journal_mode = OFF;');


    $this->time('Copying data', function () use ($tables) {
      // $this->sqliteConn->statement('PRAGMA foreign_keys = OFF;');
      
      // Copy data for each table in chunks
      foreach ($tables as $table) {
        $this->time("copying $table table", function () use ($table) {
          $this->mainConn->transaction(function () use ($table) {
            $this->copyQuery($table, $this->mainConn->table($table));
          });
        });
      }

      $this->time('copying config table', function () {
        $this->mainConn->transaction(function () {
          $this->copyQuery('config', $this->mainConn->table('config')->orderBy('key'), true);
        });
      });

      if ($this->option('no-completed-data')) {
        $ts = ['question_datum', 'datum', 'survey_condition_tag', 'section_condition_tag'];
        foreach ($ts as $table) {
          $this->time("copying incomplete data from $table table", function () use ($table) {
            $this->mainConn->transaction(function () use ($table) {
              $q = $this->mainConn->
                table($table)->
                whereIn('survey_id', function ($q) {
                  return $q->
                    select('id')->
                    from('survey')->
                    whereNull('completed_at');
                });
              $this->copyQuery($table, $q);
            });
          });
        }
        $this->time('copying incomplete data from action table', function () {
          $this->mainConn->transaction(function () {
            $q = $this->mainConn->
              table('action')->
              whereIn('interview_id', function ($q) {
                return $q->
                  select('id')->
                  from('interview')->
                  whereIn('survey_id', function ($q) {
                    return $q->
                      select('id')->
                      from('survey')->
                      whereNull('completed_at');
                  });
              });
            $this->copyQuery('action', $q);
          });
        });
        // This affected preload actions so we're just copying the edge table as-is instead.
        // $this->time('copying incomplete data from edge table', function () {
        //   $this->mainConn->transaction(function () {
        //     $q = $this->mainConn->
        //       table('edge')->
        //       whereIn('id', function ($q) {
        //         return $q->
        //           select('edge_id')->
        //           from('datum')->
        //           whereIn('survey_id', function ($q) {
        //             return $q->
        //               select('id')->
        //               from('survey')->
        //               whereNull('completed_at');
        //           });
        //       });
        //     $this->copyQuery('edge', $q);
        //   });
        // });
      }

      $this->sqliteConn->statement('PRAGMA foreign_keys = ON;');
    });

    // Create indices
    if (!$this->option('no-indices')) {
      $this->time('creating indices', function () {
        if (!$this->sqliteConn->unprepared(file_get_contents($this->indexFile))) {
          throw new Error('Unable to create indexes');
        }
      });
    }

    // Run integrity check
    $res = 1;
    if ($this->option('quick-check')) {
      $this->time('performing quick integrity check', function () use (&$res) {
        $res = $this->sqliteConn->statement('pragma quick_check');
      });
    } else {
      $this->time('performing integrity check', function () use (&$res) {
        $res = $this->sqliteConn->statement('pragma integrity_check');
      });
    }
    if (!$res) {
      throw 'Integrity check failure';
    }

    if (!$this->option('skip-foreign')) {
      $this->time('checking foreign keys', function () {
        $this->info($this->sqliteConn->statement('pragma foreign_key_check'));
      });
    }

    $this->sqliteConn->disconnect();
  }

  private function stdObjsToArrs (Collection $objs): array {
    $arr = [];
    foreach ($objs as $i => $row) {
      $arr[$i] = (array)$row;
    }
    return $arr;
  }

  private function copyQuery (string $table, $q, bool $preOrdered = false) {
    $c = 0;

    $copier = function ($rows) use ($table, &$c) {
      $insertRows = $this->stdObjsToArrs($rows);
      $this->sqliteConn->transaction(function () use ($table, $insertRows) {
        $this->sqliteConn->table($table)->insert($insertRows);
      });
      $c += count($insertRows);
    };

    if (!$preOrdered) {
      $q->chunkById($this->option('chunk-size'), $copier);
    } else {
      $q->chunk($this->option('chunk-size'), $copier);
    }
    
    $this->info("Copied $c rows into $table");
  }

  private function getTables(): array {

    $tables = [];

    // Laravel 6 add Schema::allTables() type method that abstracts this
    $tablesQuery = DB::select('SHOW TABLES');
    foreach ($tablesQuery as $raw) {
      $table = array_values(get_object_vars($raw))[0];
      $shouldInclude = !in_array($table, $this->ignoredTables) && 
        !in_array($table, $this->specialTables) && 
        (!$this->option('no-completed-data') || !in_array($table, $this->surveyTables));
      if ($shouldInclude) {
        array_push($tables, $table);
      }
    }
    return $tables;
  }
}
