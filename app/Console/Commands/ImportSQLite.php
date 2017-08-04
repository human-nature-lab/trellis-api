<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use PDO;

class ImportSQLite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trellis:import:sqlite {--exclude=*} {storage_path?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import SQLite database (currently INSERT statements only) from storage_path (or stdout if not specified). --exclude=<table> can be specified multiple times to exclude table(s) from the import. Returns either 1) the number of rows modified and exit code 0 or 2) the output of `php artisan trellis:check:mysql:foreignkeys` and exit code 1';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // for now just call ImportMySQL internally since the INSERT statements are handled the same way
        return $this->call('trellis:import:mysql', [
            '--exclude' => $this->option('exclude'),
            'storage_path' => $this->argument('storage_path'),
        ]);

        // Config::set('database.connections.sqlite', array(
        //     'driver'    => 'sqlite',
        //     'database'  => storage_path($this->argument('storage_path')),
        // ));
        //
        // $sqlite = app('db')->connection('sqlite');
        //
        // $sqlite->setFetchMode(PDO::FETCH_ASSOC);
        //
        // $rows = $sqlite->select("
        //     select * from user
        // ");
        //
        // $sqlite->setFetchMode(PDO::FETCH_CLASS);
        //
        // dump($rows);
        //
        // return 1;    //TODO decide whether to implement this command (see SyncController::uploadSync() for example).  for now return 1 to indicate failure
    }
}
