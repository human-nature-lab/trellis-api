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

        // $excludedTables = array_merge($this->option('exclude'), ['datum', 'datum_choice', 'datum_geo', 'datum_group_tag', 'datum_photo', 'edge_datum']);
        $excludedTables = array_merge($this->option('exclude'), []);

        $DB_HOST = env('DB_HOST');
        $DB_PORT = env('DB_PORT');
        $DB_USERNAME = env('DB_USERNAME');
        $DB_DATABASE = env('DB_DATABASE');

        $ignoreTablesString = implode(' ', array_map(function ($table) use ($DB_DATABASE) {
            return "--ignore-table=$DB_DATABASE." . escapeshellarg($table);
        }, $excludedTables));

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
        $mainDumpCmd = "mysqldump --host=$DB_HOST --port=$DB_PORT --user=$DB_USERNAME --net-buffer-length=$length --single-transaction --complete-insert --compact --skip-triggers $ignoreTablesString $DB_DATABASE $dumpPathString";
        if (substr($dumpPathString, 0, 1) === '>') {
            $dumpPathString = '>'.$dumpPathString;
        }
        // TODO: Think about whether it is necessary, with the new sync, to exclude datum from completed surveys
        //$datumDumpCmd = "mysqldump --host=$DB_HOST --port=$DB_PORT --user=$DB_USERNAME --net-buffer-length=$length --single-transaction --complete-insert  --compact --skip-triggers $DB_DATABASE datum --where=".'"survey_id in (select id from survey where survey.completed_at is null) or preload_id is not null"'." $dumpPathString";
        //$datumRelatedCmd = "mysqldump --host=$DB_HOST --port=$DB_PORT --user=$DB_USERNAME --net-buffer-length=$length --single-transaction --complete-insert  --compact --skip-triggers $DB_DATABASE datum_geo datum_photo datum_group_tag datum_choice edge_datum --where=".'"datum_id in (select id from datum where survey_id in (select id from survey where completed_at is null) or preload_id is not null)"'." $dumpPathString";

        //$cmds = [$mainDumpCmd, $datumDumpCmd, $datumRelatedCmd];
        $cmds = [$mainDumpCmd];
        foreach ($cmds as $cmd) {
            $process = new Process($cmd, base_path(), [
                    'DB_HOST' => env('DB_HOST'),
                    'DB_PORT' => env('DB_PORT'),
                    'DB_USERNAME' => env('DB_USERNAME'),
                    'MYSQL_PWD' => env('DB_PASSWORD'),  // use MYSQL_PWD to suppress "mysqldump: [Warning] Using a password on the command line interface can be insecure." instead of passing --password="$DB_PASSWORD"  //BUG decide whether to use mysql_config_editor
                    'DB_DATABASE' => env('DB_DATABASE'),
                ]);

            $process->setTimeout(null)->run(function ($type, $buffer) {
                fwrite($type === Process::OUT ? STDOUT : STDERR, $buffer);
            });

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
        }


        return 0;
    }
}
