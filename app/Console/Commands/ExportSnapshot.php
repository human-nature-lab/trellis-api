<?php

namespace App\Console\Commands;

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

        $snapshotPath = FileHelper::storagePath(config('snapshot.directory'));

        ///// throttle script /////

        $now = time();

        if (!$this->option('force')) {
            $files = glob("$snapshotPath/*");

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

        FileHelper::mkdir($snapshotPath);

        ///// remove old temporary files /////

        app()->configure('temp');   // save overhead by only loading config when needed

        $tempPath = FileHelper::storagePath(config('temp.directory'));

        FileHelper::mkdir($tempPath);

        $dumpPrefix = self::DUMP_PREFIX;

        $files = glob("$tempPath/$dumpPrefix*");
        $files = array_combine($files, array_map("filemtime", $files));

        foreach ($files as $file => $timestamp) {
            if ($now - $timestamp > config('temp.seconds')) {
                unlink($file);  // remove any previous dumps older than TEMP_SECONDS (possibly left behind by script crashing, etc)
            }
        }

        ///// dump sqlite /////

        Epoch::inc();

        $identifier = Epoch::hex(Epoch::get());

        $sqliteDumpPrefix = $dumpPrefix . 'sqlite_';
        $sqliteDumpName = $sqliteDumpPrefix . $identifier . '.sql';
        $sqliteDumpPath = "$tempPath/$sqliteDumpName";

        $this->call('trellis:export:sqlite', [
            'storage_path' => config('temp.directory') . "/$sqliteDumpName", // pass argument as local path inside storage path
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

        ///// move zip file to destination atomically /////

        $sourcePath = $sqliteDumpPath . '.gz';
        $snapshotName = $identifier . '.sqlite.sql';
        $destPath = $snapshotPath . '/' . $snapshotName . '.gz';

        return rename($sourcePath, $destPath);
    }
}
