<?php

namespace App\Console\Commands;

use App\Library\DatabaseHelper;
use App\Library\FileHelper;
use App\Models\Epoch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
// use Illuminated\Console\WithoutOverlapping;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use App\Models\Snapshot;
use App\Models\Sync;
use Ramsey\Uuid\Uuid;
use DB;

class ExportSnapshotV2 extends Command
{
    // USE: sudo -u www-data php artisan trellis:export:snapshotv2
    // use WithoutOverlapping;

    // TODO: Why doesn't this mutex strategy work?
    //protected $mutexStrategy = 'mysql';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trellis:export:snapshotv2 {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Check if snapshot creation is necessary and export snapshot if snapshot is out of date";

    const DUMP_PREFIX = 'dump_';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Starting export...");
        app()->configure('snapshot');   // save overhead by only loading config when needed

        $databaseName = env('DB_DATABASE');
        $this->info("databaseName: $databaseName");

        $snapshotDirPath = FileHelper::storagePath(config('snapshot.directory.path'));
        $this->info("snapshotDirPath: $snapshotDirPath");

        $excludeTables = array_keys(array_filter(config('snapshot.substitutions.download'), function ($fields) {
            return $fields == [
                    '*' => null,
                ];    // for now, can only exclude entire tables with wildcard
        }));

        $excludeTablesString = implode(',', array_map(function ($table) {
            return escapeshellarg($table);
        }, $excludeTables));
        $this->info("excludeTablesString: $excludeTablesString");

        $snapshotCreationNeeded = false;

        // First check if there has been an upload since the last snapshot creation
        $latestSnapshot = Snapshot::where('deleted_at',null)
            ->orderBy('created_at', 'desc')
            ->first();
        $this->info("latestSnapshot: " . $latestSnapshot['created_at']);

        $latestUpload = Sync::where('deleted_at', null)
            ->where('type', 'upload')
            ->orderBy('created_at', 'desc')
            ->first();
        $this->info("latestUpload: " . $latestUpload['created_at']);

        if ($latestSnapshot == null) {
            // Always create a new snapshot if the snapshot table is empty
            $snapshotCreationNeeded = true;
            $this->info("No snapshot found, always create snapshot.");
        } else if ($latestUpload != null) {
            // Otherwise, create a snapshot if there is a newer upload than the latest snapshot
            $this->info("unix timestamp of latestSnapshot: " . strtotime($latestSnapshot['created_at']));
            $this->info("unix timestamp of latestUpload: " . strtotime($latestUpload['created_at']));
            $snapshotCreationNeeded = (strtotime($latestSnapshot['created_at']) < strtotime($latestUpload['created_at']));
            $this->info("latestSnapshot older than latestUpload?: " . (($snapshotCreationNeeded) ? "true" : "false"));
        }

        if (!$snapshotCreationNeeded) {
            // Check the latest updated table that is synced
            $this->info("Checking the latest table update using the information_schema database.");
            $latestUpdateQuery = "select max(update_time) as latest_update_time " .
                                 "from information_schema.tables " .
                                 "where table_schema = '$databaseName' " .
                                 "and table_name not in ($excludeTablesString);";

            $this->info("latestUpdateQuery: " . $latestUpdateQuery);
            $latestUpdate = DB::select($latestUpdateQuery);
            $this->info("unix timestamp of latestSnapshot: " . strtotime($latestSnapshot['created_at']));
            $this->info("unix timestamp of latestUpdate: " . strtotime($latestUpdate[0]->latest_update_time));
            $snapshotCreationNeeded = (strtotime($latestSnapshot['created_at']) < strtotime($latestUpdate[0]->latest_update_time));
            $this->info("latestSnapshot older than latestUpdate?: " . (($snapshotCreationNeeded) ? "true" : "false"));
        }

        if ($snapshotCreationNeeded) {
            $this->info("Snapshot creation is needed, starting snapshot generation.");
            $snapshotId = Uuid::uuid4();
            $this->info("snapshotId: $snapshotId");

            ///// remove old temporary files /////
            $this->info("Removing old temporary files.");
            app()->configure('temp');   // save overhead by only loading config when needed
            $tempDirPath = FileHelper::storagePath(config('temp.directory.path'));
            $this->info("tempDirPath: $tempDirPath");
            FileHelper::mkdir($tempDirPath);
            FileHelper::cleanDirectory($tempDirPath, config('temp.directory.size.max'));

            ///// dump sqlite and zip output /////
            $excludeTablesString = implode(' ', array_map(function ($table) {
                return "--exclude=" . escapeshellarg($table);
            }, $excludeTables));
            $this->info("excludeTablesString: $excludeTablesString");

            $sqliteDumpPrefix = self::DUMP_PREFIX . 'sqlite_';
            $this->info("sqliteDumpPrefix: $sqliteDumpPrefix");
            $sqliteDumpName = $sqliteDumpPrefix . $snapshotId . '.sql';
            $this->info("sqliteDumpName: $sqliteDumpName");
            $sqliteDumpPath = "$tempDirPath/$sqliteDumpName";
            $this->info("sqliteDumpPath: $sqliteDumpPath");
            $zipPath = $sqliteDumpPath . '.zip';
            $this->info("zipPath: $zipPath");
            $zipPathString = escapeshellarg($zipPath);
            $this->info("zipPathString: $zipPathString");


            $this->info("Starting trellis:export:sqlite process...");
            $process = new Process(<<<EOT
php artisan trellis:export:sqlite $excludeTablesString > $sqliteDumpPath
EOT
                , base_path());

            $process->setTimeout(null)->run(function ($type, $buffer) {
                fwrite($type === Process::OUT ? STDOUT : STDERR, $buffer);
            });

            if (!$process->isSuccessful()) {
                $this->error('trellis:export:sqlite process failed!');
                throw new ProcessFailedException($process);
            }

            $this->info("Zipping sqlite export...");
            $process = new Process(<<<EOT
zip -j $zipPath $sqliteDumpPath
EOT
                , base_path());

            $process->setTimeout(null)->run(function ($type, $buffer) {
                fwrite($type === Process::OUT ? STDOUT : STDERR, $buffer);
            });

            if (!$process->isSuccessful()) {
                $this->error('trellis:export:sqlite process failed!');
                throw new ProcessFailedException($process);
            }

            $this->info("Removing old snapshot files...");
            ///// remove old storage files /////
            FileHelper::mkdir($snapshotDirPath);
            FileHelper::cleanDirectory($snapshotDirPath, config('snapshot.directory.size.max') - filesize($zipPath), 'getBasename');    // reserve room for new snapshot by subtracting its size

            $this->info("Moving temporary file to snapshot directory...");
            ///// move zip file to destination atomically /////
            $snapshotName = $snapshotId . '.sqlite.sql.zip';
            $this->info("snapshotName: $snapshotName");
            $snapshotPath = $snapshotDirPath . '/' . $snapshotName;
            $this->info("snapshotPath: $snapshotPath");

            $renameResult = rename($zipPath, $snapshotPath);

            if ($renameResult) {
                $this->info("Snapshot creation successful!");
                $snapshotModel = new Snapshot;
                $snapshotModel->id = $snapshotId;
                $snapshotModel->file_name = $snapshotName;
                $md5Hash = hash_file("md5", $snapshotPath);
                $this->info("calculated MD5 hash: $md5Hash");
                $snapshotModel->hash = $md5Hash;
                $snapshotModel->save();
                return 0;
            } else {
                $this->error("Snapshot creation failed!");
                return 1;
            }
            //return (rename($zipPath, $snapshotPath) != true)*1; // return 0 for success, 1 for failure
        }

        // No snapshot needed
        $this->info("No snapshot creation needed.");
        return 0;
    }
}
