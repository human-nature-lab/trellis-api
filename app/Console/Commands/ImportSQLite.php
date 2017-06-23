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
    protected $signature = 'trellis:import:sqlite {storage_path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import SQLite database';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Config::set('database.connections.sqlite', array(
            'driver'    => 'sqlite',
            'database'  => storage_path($this->argument('storage_path')),
        ));

        $sqlite = app('db')->connection('sqlite');

        $sqlite->setFetchMode(PDO::FETCH_ASSOC);

        $rows = $sqlite->select("
            select * from user
		");

        $sqlite->setFetchMode(PDO::FETCH_CLASS);

        dump($rows);
    }
}
