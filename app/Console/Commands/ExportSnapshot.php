<?php

namespace App\Console\Commands;

use App\Library\FileHelper;
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
    protected $signature = 'trellis:export:snapshot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export snapshot';

    const DUMP_PREFIX = 'dump_';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        app()->configure('temp');   // save overhead by only loading config when needed

        $tempPath = FileHelper::storagePath(config('temp.directory'));

        FileHelper::mkdir($tempPath);

        ///// remove old temporary files /////

        $dumpPrefix = self::DUMP_PREFIX;

        $files = glob("$tempPath/$dumpPrefix*");
        $files = array_combine($files, array_map("filemtime", $files));
        $now = time();

        foreach ($files as $file => $timestamp) {
            if ($now - $timestamp > config('temp.seconds')) {
                unlink($file);  // remove any previous dumps older than TEMP_SECONDS (possibly left behind by script crashing, etc)
            }
        }

        ///// dump sqlite /////

        $identifier = sha1(microtime() . random_bytes(16));

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

        app()->configure('snapshot');   // save overhead by only loading config when needed
        
        $snapshotPath = FileHelper::storagePath(config('snapshot.directory'));

        FileHelper::mkdir($snapshotPath);

        $sourcePath = $sqliteDumpPath . '.gz';
        $destPath = $snapshotPath . '/' . $sqliteDumpName . '.gz';

        return rename($sourcePath, $destPath);
    }
}
