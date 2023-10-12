<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class SyncSqliteSchema extends Command {

  protected $signature = 'trellis:sync-sqlite-schema';

  protected $description = 'Synchronize sqlite schema files with the current state of the database. Requires mysqldump, grep and sqlite3 cli tools';

  public $extraIndices = [
    'respondent_name' => ['']
  ];

  public function handle() {
    set_time_limit(0);
    app()->configure('snapshot');
    app()->configure('database');

    $user = config('database.connections.mysql.username');
    $host = config('database.connections.mysql.host');
    $pass = config('database.connections.mysql.password');
    $db = config('database.connections.mysql.database');

    $ignoreStr = '';
    $ignoredTables = config('snapshot.ignoredTables');
    foreach ($ignoredTables as $t) {
      $ignoreStr .= "--ignore-table=$db.$t ";
    }

    $schemaFile = config('snapshot.sqliteSchema');
    $indexFile = config('snapshot.sqliteIndex');

    $dumpCmd = "mysqldump --no-data --skip-triggers --compact -u$user -p$pass -h$host $ignoreStr $db | awk -f app/Console/Scripts/strip-views.awk | ./app/Console/Scripts/mysql2sqlite/mysql2sqlite -"; 
    $schemaCmd = "$dumpCmd | grep -v 'CREATE INDEX' > $schemaFile";
    $indexCmd = "$dumpCmd | grep 'CREATE INDEX' > $indexFile";
    $process = new Process($schemaCmd, base_path());
    $process->run(function ($type, $buffer) {
        fwrite($type === Process::OUT ? STDOUT : STDERR, $buffer);
    });
    $this->info("made schema from $db database");
    if (!$process->isSuccessful()) {
        throw new ProcessFailedException($process);
    }
    $process = new Process($indexCmd, base_path());
    $process->run(function ($type, $buffer) {
        fwrite($type === Process::OUT ? STDOUT : STDERR, $buffer);
    });
    $this->info("made indexes from $db database");

    if (!$process->isSuccessful()) {
        throw new ProcessFailedException($process);
    }
  }

}