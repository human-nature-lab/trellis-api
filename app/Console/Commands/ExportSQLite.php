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
    protected $signature = 'trellis:export:sqlite {--exclude=*} {storage_path?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export SQLite database to storage_path (or stdout if not specified). --exclude=<table> can be specified multiple times to exclude table(s) from the dump';

    const MYSQL_2_SQLITE = 'app/Console/Scripts/mysql2sqlite/mysql2sqlite';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $mysql2sqlite = base_path() . '/' . self::MYSQL_2_SQLITE;

        if (!is_executable($mysql2sqlite)) {
            $this->error("Please run `chmod 0770 $mysql2sqlite` to make the script executable");

            return 1;
        }

        ///// dump sqlite /////

        $excludeTablesString = implode(' ', array_map(function ($table) {
            return "--exclude=" . escapeshellarg($table);
        }, $this->option('exclude')));

        if (!is_null($this->argument('storage_path'))) {
            $dumpPath = FileHelper::storagePath($this->argument('storage_path'));

            FileHelper::mkdir(dirname($dumpPath));

            $dumpPathString = '> ' . escapeshellarg($dumpPath);
        } else {
            $dumpPathString = '';
        }

        $process = new Process(<<<EOT
php artisan trellis:export:mysql $excludeTablesString | $mysql2sqlite - $dumpPathString
EOT
, base_path());

        $process->setTimeout(null)->run(function ($type, $buffer) {
            fwrite($type === Process::OUT ? STDOUT : STDERR, $buffer);
        });

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return 0;
    }
}
