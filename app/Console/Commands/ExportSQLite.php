<?php

namespace App\Console\Commands;

use App\Library\FileHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ExportSQLite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trellis:export:sqlite {storage_path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export SQLite database';

    const DUMP_PREFIX = 'dump_';
    const MYSQL_2_SQLITE = 'app/Console/Scripts/mysql2sqlite/mysql2sqlite';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $mysql2sqlite = self::MYSQL_2_SQLITE;

        if (!is_executable($mysql2sqlite)) {
            echo "Please run `chmod 0770 $mysql2sqlite` to make the script executable" . PHP_EOL;

            return 1;
        }

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

        ///// dump mysql /////

        $identifier = sha1(microtime() . random_bytes(16));

        $mysqlDumpPrefix = $dumpPrefix . 'mysql_';
        $mysqlDumpName = $mysqlDumpPrefix . $identifier . '.sql';
        $mysqlDumpPath = "$tempPath/$mysqlDumpName";

        $this->call('trellis:export:mysql', [
            'storage_path' => config('temp.directory') . "/$mysqlDumpName", // pass argument as local path inside storage path
        ]);

        ///// dump sqlite /////

        $sqliteDumpPath = FileHelper::storagePath($this->argument('storage_path'));

        FileHelper::mkdir(dirname($sqliteDumpPath));

        $mysqlDumpPathEscaped = escapeshellarg($mysqlDumpPath);
        $sqliteDumpPathEscaped = escapeshellarg($sqliteDumpPath);

        $process = new Process(<<<EOT
$mysql2sqlite $mysqlDumpPathEscaped > $sqliteDumpPathEscaped
EOT
);

        $process->setTimeout(null)->run(function ($type, $buffer) {
            fwrite($type === Process::OUT ? STDOUT : STDERR, $buffer);
        });

        unlink($mysqlDumpPath);

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return 0;
    }
}