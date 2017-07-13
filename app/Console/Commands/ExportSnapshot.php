<?php

namespace App\Console\Commands;

use App\Library\DatabaseHelper;
use App\Library\FileHelper;
use App\Models\Epoch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminated\Console\WithoutOverlapping;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ExportSnapshot extends Command
{
    use WithoutOverlapping;

    protected $mutexStrategy = 'mysql';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trellis:export:snapshot {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Export snapshot, throttled to every env('SNAPSHOT_SECONDS_MIN') unless --force is used";

    const DUMP_PREFIX = 'dump_';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        app()->configure('snapshot');   // save overhead by only loading config when needed

        $snapshotDirPath = FileHelper::storagePath(config('snapshot.directory.path'));

        ///// check if dump for current epoch and database timestamp already exists /////

        if (!$this->option('force')) {
            $identifier = Epoch::hex(Epoch::get());
            $snapshotName = $identifier . '.sqlite.sql';
            $snapshotPath = $snapshotDirPath . '/' . $snapshotName . '.gz';

            if (file_exists($snapshotPath)) {
                $databaseTimestamp = DatabaseHelper::databaseModifiedAt();
                $snapshotTimestamp = filemtime($snapshotPath);

                if ($databaseTimestamp == $snapshotTimestamp) {
                    $this->error("Snapshot for current epoch already exists");

                    return 1;   //NOTE this is the only place that compares file and database timestamps (could also set the filename to timestamp for better consistency)
                }
            }
        }

        ///// throttle script /////

        $now = time();

        if (!$this->option('force')) {
            $files = glob("$snapshotDirPath/*");

            if (count($files)) {
                $files = array_combine($files, array_map("filemtime", $files));
                $newestFilename = array_keys($files, max($files))[0];
                $newestTimestamp = $files[$newestFilename];

                if ($now - $newestTimestamp < config('snapshot.seconds.min')) {
                    $this->error("Not enough time has passed since last snapshot, please try again in about " . config('snapshot.seconds.min') . " seconds");

                    return 1;
                }
            }
        }

        ///// remove old temporary files /////

        app()->configure('temp');   // save overhead by only loading config when needed

        $tempDirPath = FileHelper::storagePath(config('temp.directory.path'));

        FileHelper::mkdir($tempDirPath);
        FileHelper::cleanDirectory($tempDirPath, config('temp.directory.size.max'));

        ///// dump sqlite /////

        $identifier = Epoch::hex(Epoch::inc());
        $sqliteDumpPrefix = self::DUMP_PREFIX . 'sqlite_';
        $sqliteDumpName = $sqliteDumpPrefix . $identifier . '.sql';
        $sqliteDumpPath = "$tempDirPath/$sqliteDumpName";
        $databaseTimestamp = DatabaseHelper::databaseModifiedAt();    // get database timestamp just before dump begins (any later updates will be detected by comparing database and dump file timestamps)

        $this->call('trellis:export:sqlite', [
            'storage_path' => config('temp.directory.path') . "/$sqliteDumpName", // pass argument as local path inside storage path
        ]);

        ///// zip sqlite /////

        $sqliteDumpPathEscaped = escapeshellarg($sqliteDumpPath);

        $process = new Process(<<<EOT
gzip --best $sqliteDumpPathEscaped
EOT
);

        $process->setTimeout(null)->run(function ($type, $buffer) {
            fwrite($type === Process::OUT ? STDOUT : STDERR, $buffer);
        });

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $zipPath = $sqliteDumpPath . '.gz';

        touch($zipPath, $databaseTimestamp); // set file timestamp to database timestamp to optimize checking if snapshot already exists during future snapshot creations

        ///// remove old storage files /////

        FileHelper::mkdir($snapshotDirPath);
        FileHelper::cleanDirectory($snapshotDirPath, config('snapshot.directory.size.max') - filesize($zipPath), 'getBasename');    // reserve room for new snapshot by subtracting its size

        ///// move zip file to destination atomically /////

        $snapshotName = $identifier . '.sqlite.sql';
        $snapshotPath = $snapshotDirPath . '/' . $snapshotName . '.gz';

        return rename($zipPath, $snapshotPath);
    }
}
