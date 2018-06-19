<?php

namespace App\Console\Commands;

use App\Library\DatabaseHelper;
use Faker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDO;

class CheckMySQLForeignKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trellis:check:mysql:foreignkeys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check MySQL foreign key consistency.  Prints JSON string of "[table_name: [column_name: [missing_foreign_key1, missing_foreign_key2, ...]]]" for any missing foreign keys';

    /**
     * Execute the console command.
     *
     * To call from within code:
     *
     * ob_start();
     *
     * \Illuminate\Support\Facades\Artisan::call('trellis:check:mysql:foreignkeys');
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

//        DB::setFetchMode(PDO::FETCH_ASSOC);
        $tables = [];
        foreach (DB::select("show tables") as $row) {
            foreach ($row as $key => $val) {
                array_push($tables, $val);
            }
        }
        $foreignKeys = DatabaseHelper::foreignKeys();
        $tableColumnForeignKeys = array_merge(array_combine($tables, array_fill(0, count($tables), [])), collect($foreignKeys)->groupBy('table_name')->map(function ($attributes) {
            return collect($attributes)->keyBy('column_name')->toArray();
        })->toArray()); // keys contain every table but some values are an empty array if no columns are foreign keys
        $referencingTables = array_values(array_sort(array_unique(array_column($foreignKeys, 'table_name')), function ($value) {
            return $value;
        }));

        // check any tables that contain foreign keys to other tables to make sure keys in other tables exist
        $missingForeignKeys = array_filter(array_map(function ($referencingTable) use ($tableColumnForeignKeys) {
            $columnForeignKeys = $tableColumnForeignKeys[$referencingTable];

            return array_filter(array_map(function ($foreignKey) {
                return array_unique(array_pluck(DB::table($foreignKey['table_name'] . ' as t1')
                    ->select('t1.' . $foreignKey['column_name'])
                    ->leftJoin($foreignKey['referenced_table_name'] . ' as t2', 't1.' . $foreignKey['column_name'], '=', 't2.' . $foreignKey['referenced_column_name'])
                    ->whereNotNull('t1.' . $foreignKey['column_name'])
                    ->whereNull('t2.' . $foreignKey['referenced_column_name'])
                    ->get(), $foreignKey['column_name']));
            }, $columnForeignKeys));
        }, array_combine($referencingTables, $referencingTables)));

        echo json_encode($missingForeignKeys, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;

        return 0;
    }
}
