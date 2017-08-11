<?php

namespace App\Console\Commands;

use App\Library\DatabaseHelper;
use DB;
use Faker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use PDO;

class ShowMySQLJSON extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trellis:show:mysql:json {--database= : Optional name of database to use}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show MySQL schema as JSON.  Prints JSON string of "[table_name: [column_name: type]]"';

    /**
     * Execute the console command.
     *
     * To call from within code:
     *
     * ob_start();
     *
     * \Illuminate\Support\Facades\Artisan::call('trellis:show:mysql:json');
     *
     * $result = json_decode(ob_get_clean(), true);
     *
     * @return mixed
     */
    public function handle()
    {
        if (config('database.default') != 'mysql') {
            $this->error('Currently `php artisan ' . $this->signature . '` only works with MySQL.');

            return 1;
        }

        return DatabaseHelper::useDatabase($this->option('database'), function () {
            return DatabaseHelper::fetch(PDO::FETCH_ASSOC, function () {
                $tableColumns = collect(DatabaseHelper::tables())->flip()->map(function ($value, $table) {
                    return collect(DatabaseHelper::columns($table))->map(function ($column) {
                        return DatabaseHelper::schemaJSON($column);
                    });
                });

                echo json_encode($tableColumns, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;

                return 0;
            });
        });
    }
}
