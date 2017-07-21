<?php

namespace App\Console\Commands;

use App\Library\FileHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ExportMySQL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trellis:export:mysql {--exclude=*} {storage_path?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export MySQL database to storage_path (or stdout if not specified). --exclude=<table> can be specified multiple times to exclude table(s) from the dump';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $db = escapeshellarg(config('database.connections.mysql.database'));
        $dbHost = escapeshellarg(config('database.connections.mysql.host'));
        $dbPort = escapeshellarg(config('database.connections.mysql.port'));
        // $dbUsername = escapeshellarg(config('database.connections.mysql.username'));
        // $dbPassword = escapeshellarg(config('database.connections.mysql.password'));
        $ignoreTablesString = implode(' ', array_map(function ($table) use ($db) {
            return "--ignore-table=$db." . escapeshellarg($table);
        }, $this->option('exclude')));

        if (!is_null($this->argument('storage_path'))) {
            $dumpPath = FileHelper::storagePath($this->argument('storage_path'));

            FileHelper::mkdir(dirname($dumpPath));

            $dumpPathString = '> ' . escapeshellarg($dumpPath);
        } else {
            $dumpPathString = '';
        }

        // # to save encrypted password in ~/.mylogin.cnf run:
        // mysql_config_editor set --login-path=client --host=localhost --user=homestead --password
        // # to show ~/.mylogin.cnf run:
        // mysql_config_editor print --all
        // # to run mysql utilities with config:
        // mysqldump --login-path=client --host $dbHost --port $dbPort --single-transaction --skip-extended-insert --compact trellis > trellis_mysql.sql
        $process = new Process(<<<EOT
mysqldump --host $dbHost --port $dbPort --single-transaction --skip-extended-insert --complete-insert --compact $ignoreTablesString $db $dumpPathString
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
