<?php

namespace App\Console\Commands;

use App\Library\FileHelper;
use Illuminate\Console\Command;
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
        $length = escapeshellarg(10*1024);    //TODO get this from .env or derive it as: length <= (smallest insert statement)*SQLITE_MAX_COMPOUND_SELECT.  for example 24*500 = 12000.  use 10k for now
        $ignoreTablesString = implode(' ', array_map(function ($table) {
            return '--ignore-table="$DB_DATABASE".' . escapeshellarg($table);
        }, $this->option('exclude')));

        if (!is_null($this->argument('storage_path'))) {
            $dumpPath = FileHelper::storagePath($this->argument('storage_path'));

            FileHelper::mkdir(dirname($dumpPath));

            $dumpPathString = '> ' . escapeshellarg($dumpPath);
        } else {
            $dumpPathString = '';
        }

        // # (optional) to save encrypted password in ~/.mylogin.cnf run:
        // mysql_config_editor set --login-path=client --host=localhost --user=homestead --password
        // # to show ~/.mylogin.cnf run:
        // mysql_config_editor print --all
        // # to run mysql utilities with config:
        // mysqldump --login-path=client --host="\$DB_HOST" --port="\$DB_PORT" --single-transaction --skip-extended-insert --compact trellis > trellis_mysql.sql
        $process = new Process(<<<EOT
mysqldump --host="\$DB_HOST" --port="\$DB_PORT" --user="\$DB_USERNAME" --password="\$DB_PASSWORD" --net-buffer-length=$length --single-transaction --complete-insert --compact $ignoreTablesString "\$DB_DATABASE" $dumpPathString
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
