<?php

namespace App\Console\Commands;

use DB;
use Faker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use PDO;

class CheckMySQL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trellis:check:mysql';

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
     * \Illuminate\Support\Facades\Artisan::call('trellis:check:mysql');
     *
     * $result = json_decode(ob_get_clean(), true);
     *
     * @return mixed
     */
    public function handle()
    {
        if (config('database.default') != 'mysql') {
            echo 'Currently `php artisan ' . $this->signature . '` only works with MySQL.' . PHP_EOL;

            return -1;
        }

        DB::setFetchMode(PDO::FETCH_ASSOC);

        $tables = $this->tables();
        $foreignKeys = $this->foreignKeys();
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

    /**
     * Returns an array of all of the tables in the current database.
    */
    public function tables()
    {
        return array_flatten(DB::select("
            show tables;
        "));
    }

    /**
     * Returns array of the form:
     *
     * [
     *   0 => [
     *     "table_name" => "table_name",
     *     "column_name" => "column_name",
     *     "referenced_table_name" => "referenced_table_name",
     *     "referenced_column_name" => "referenced_column_name",
     *     "update_rule" => "NO ACTION",
     *     "delete_rule" => "NO ACTION",
     *     "constraint_name" => "fk__constraint_name",
     *   ],
     * ]
     */
    public function foreignKeys()
    {
        return DB::select("
            select
                information_schema.key_column_usage.table_name,
                information_schema.key_column_usage.column_name,
                information_schema.key_column_usage.referenced_table_name,
                information_schema.key_column_usage.referenced_column_name,
                information_schema.referential_constraints.update_rule,
                information_schema.referential_constraints.delete_rule,
                information_schema.key_column_usage.constraint_name
            from
                information_schema.key_column_usage
            join
                information_schema.referential_constraints
            on
                information_schema.key_column_usage.constraint_name = information_schema.referential_constraints.constraint_name
            where
                referenced_table_schema = ?
            order by
                table_name,
                column_name;
        ", [config('database.connections.mysql.database')]);
    }
}
