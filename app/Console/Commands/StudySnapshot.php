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
use App\Library\FileMutex;
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
    {--no-inaccessible-data}
    {--vacuum}';

  protected $description = 'Take all of the data related to a study and put it into a sqlite database for syncing';

  private $schemaFile = '';
  private $indexFile = '';
  private $mut;

  private $sqliteConn;
  private $mainConn;
  private $snapshotService;
  private $ignoredTables = [];
  private $specialTables = ['config'];
  private $customTables = ['action'];
  private $surveyTables = ['datum', 'question_datum', 'survey_condition_tag', 'section_condition_tag'];

  public function handle(SnapshotService $ss) {
    $this->snapshotService = $ss;
    set_time_limit(0);
    app()->configure('temp');
    app()->configure('snapshot');
    $this->schemaFile = config('snapshot.sqliteSchema');
    $this->indexFile = config('snapshot.sqliteIndex');
    $this->ignoredTables = config('snapshot.ignoredTables');

    $this->mut = new FileMutex(storage_path('locks/snapshot'), 30 * 60 * 1000);
    if ($this->mut->isLocked()) {
      $this->error('snapshot already running');
      Log::error('snapshot already running');
      return 2;
    }

    return $this->mut->do(function () {
      return $this->do();
    });
  }

  private function do () {

    if (!$this->option('force')) {
      $isOutdated = $this->time('checking for database changes', function () {
        return $this->snapshotService->snapshotIsOutdated();
      });
  
      if ($isOutdated) {
        $this->info('A new snapshot needs to be created!');
      } else {
        $this->info('The latest snapshot matches current database');
        return 3;
      }
    }
    
    try {
      $this->time('pre-hooks', function () {
        Log::info($this->runPreHooks());
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
        Log::info($this->runPostHooks());
      });
    } catch (Exception $e) {
      Log::error($e);
      return 1;
    }  

  }

  private function runPreHooks () {
    $hookService = new HookService();
    $hooks = $hookService->getPreSnapshotHooks();
    Log::info("running pre-snapshot hooks:", $hooks);
    $results = [];
    foreach ($hooks as $hook) {
      $hook->setup();
      $results[$hook->def['id']] = $hook->run();
    }
    return $results;
  }

  private function runPostHooks () {
    $hookService = new HookService();
    $hooks = $hookService->getPostSnapshotHooks();
    Log::info("running post-snapshot hooks:", $hooks);
    $results = [];
    foreach ($hooks as $hook) {
      $hook->setup();
      $results[$hook->def['id']] = $hook->run();
    }
    return $results;
  }

  private function runIt () {
    $sqliteLocation = config('database.connections.sqlite_snapshot.database');

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
        ->orderBy('updated_at', 'desc')
        ->first();

    $this->info("latestSnapshot: " . $latestSnapshot['updated_at']);

    $latestUpload = Upload::where('deleted_at', null)
        ->where('status', 'SUCCESS')
        ->orderBy('updated_at', 'desc')
        ->first();
    $this->info("latestUpload: " . $latestUpload['updated_at']);

    if ($latestSnapshot == null) {
        // Always create a new snapshot if the snapshot table is empty
        $hasChanged = true;
        $this->info("No snapshot found, always create snapshot.");
    } else if ($latestUpload != null) {
        // Otherwise, create a snapshot if there is a newer upload than the latest snapshot
        $this->info("unix timestamp of latestSnapshot: " . strtotime($latestSnapshot['updated_at']));
        $this->info("unix timestamp of latestUpload: " . strtotime($latestUpload['updated_at']));
        $hasChanged = (strtotime($latestSnapshot['updated_at']) < strtotime($latestUpload['updated_at']));
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
      $this->info("unix timestamp of latestSnapshot: " . strtotime($latestSnapshot['updated_at']));
      $this->info("unix timestamp of latestUpdate: " . strtotime($latestUpdate->latest_update_time));
      $snapshotCreationNeeded =  ( ($latestUpdateTime == null) || (strtotime($latestSnapshot['updated_at']) < strtotime($latestUpdateTime)) );
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
      $this->sqliteConn = DB::connection('sqlite_snapshot');
      $this->mainConn = DB::connection('mysql_snapshot');

      $tables = $this->getTables();

      // Create initial schema in the snapshot
      if (!$this->sqliteConn->unprepared(file_get_contents($this->schemaFile))) {
        throw new Error('Failed to created schema');
      };
    });

    $this->sqliteConn->statement('PRAGMA synchronous = OFF;');
    $this->sqliteConn->statement('PRAGMA journal_mode = OFF;');
    $this->sqliteConn->statement('PRAGMA foreign_keys = OFF;');

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

      if ($this->option('no-inaccessible-data')) {
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

        // $this->sqliteConn->statement('PRAGMA foreign_keys = ON;');
        

        // $this->time('copying incomplete data from preload_action table', function () {
        //   $this->mainConn->transaction(function () {
        //     $q = $this->mainConn->
        //       table('preload_action')->
        //       whereNull('deleted_at')->
        //       whereIn('question_id', function ($q) {
        //         return $q->
        //         select('id')->
        //         from('question')->
        //         whereNull('deleted_at')->
        //         whereIn('question_group_id', function ($q) {
        //           return $q->
        //           select('question_group_id')->
        //           from('section_question_group')->
        //           whereIn('section_id', function ($q) {
        //             return $q->
        //             select('section_id')->
        //             from('form_section')->
        //             whereNull('deleted_at')->
        //             whereIn('form_id', function ($q) {
        //               return $q->
        //               select('current_version_id')->
        //               from('study_form')->
        //               whereNull('deleted_at');
        //             });
        //         });
        //       });
        //     });
        //     $this->copyQuery('preload_action', $q, false, false, 100);
        //   });
        // });
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
    });

    // Create indices
    if (!$this->option('no-indices')) {
      $this->time('creating indices', function () {
        if (!$this->sqliteConn->unprepared(file_get_contents($this->indexFile))) {
          throw new Error('Unable to create indexes');
        }
      });
    }

    $this->sqliteConn->statement('PRAGMA foreign_keys = ON;');

    $this->time('removing unused data', function () {
      $deleteUnusedPreloadActions = "
        delete from preload_action 
        where id not in (select preload_action_id from action where preload_action_id is not null)
        and question_id not in (
          select id from question where deleted_at is null and question_group_id in (
            select question_group_id from section_question_group
            where deleted_at is null and section_id in (
              select section_id from form_section where deleted_at is null and form_id in (
                select current_version_id from study_form
                where deleted_at is null
              )
            )
          )
        )";
      $deleted = $this->sqliteConn->delete($deleteUnusedPreloadActions);
      $this->info("deleted $deleted rows from preload_action table");
    });

    if ($this->option('vacuum')) {
      $this->time('vacuuming', function () {
        $sizeBefore = $this->getSqliteSize();
        $this->sqliteConn->statement('vacuum');
        $delta = $sizeBefore - $this->getSqliteSize();
        $this->info("reduced size by $delta bytes");
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

  private function getSqliteSize(): int {
    $sqliteConnected = isset($this->sqliteConn);
    if ($sqliteConnected) {
      $this->sqliteConn->disconnect();
    }
    $res = filesize(config('database.connections.sqlite_snapshot.database'));
    if ($sqliteConnected) {
      $this->sqliteConn->reconnect();
    }
    return $res === false ? 0 : $res;
  }

  private function copyQuery (string $table, $q, bool $preOrdered = false, bool $chunked = true, int $chunkSize = 0) {
    $c = 0;
    if ($chunkSize === 0) {
      $chunkSize = $this->option('chunk-size');
    }

    $copier = function ($rows) use ($table, &$c) {
      $insertRows = $this->stdObjsToArrs($rows);
      $this->sqliteConn->transaction(function () use ($table, $insertRows) {
        $this->sqliteConn->table($table)->insert($insertRows);
      });
      $c += count($insertRows);
    };

    if ($chunked) {
      if (!$preOrdered) {
        $q->chunkById($chunkSize, $copier);
      } else {
        $q->chunk($chunkSize, $copier);
      }
    } else {
      // Insert with a single cursor instead
      $chunk = [];
      foreach($q->cursor() as $item) {
        array_push($chunk, $item);
        if (count($chunk) === $chunkSize) {
          $copier($chunk);
          $chunk = [];
        }
      }
      if (count($chunk) > 0) {
        $copier($chunk);
      }
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
      (!$this->option('no-inaccessible-data') || !in_array($table, $this->surveyTables) &&
      !in_array($table, $this->customTables)
      );
      if ($shouldInclude) {
        array_push($tables, $table);
      }
    }
    return $tables;
  }
}
