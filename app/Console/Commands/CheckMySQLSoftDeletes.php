<?php

namespace App\Console\Commands;

use App\Library\DatabaseHelper;
use DB;
use Faker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use PDO;

class CheckMySQLSoftDeletes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trellis:check:mysql:softdeletes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check MySQL soft delete consistency.  Prints JSON string of "[table_name: [column_name: [invalid_soft_delete1, invalid_soft_delete2, ...]]]" for invalid soft deletes';

    /**
     * Execute the console command.
     *
     * To call from within code:
     *
     * ob_start();
     *
     * \Illuminate\Support\Facades\Artisan::call('trellis:check:mysql:softdeletes');
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

        DB::setFetchMode(PDO::FETCH_ASSOC);

        $tables = DatabaseHelper::tables();
        $foreignKeys = DatabaseHelper::foreignKeys();
        $tableColumnForeignKeys = array_merge(array_combine($tables, array_fill(0, count($tables), [])), collect($foreignKeys)->groupBy('table_name')->map(function ($attributes) {
            return collect($attributes)->keyBy('column_name')->toArray();
        })->toArray()); // keys contain every table but some values are an empty array if no columns are foreign keys
        $referencingTables = array_values(array_sort(array_unique(array_column($foreignKeys, 'table_name')), function ($value) {
            return $value;
        }));

        // check any tables that contain foreign keys to other tables to make sure deleted_at is set if referenced table row's deleted_at is not null
        $missingForeignKeys = array_filter(array_map(function ($referencingTable) use ($tableColumnForeignKeys) {
            $columnForeignKeys = $tableColumnForeignKeys[$referencingTable];

            return array_filter(array_map(function ($foreignKey) {
                return array_unique(array_pluck(DB::table($foreignKey['table_name'] . ' as t1')
                    ->select('t1.' . $foreignKey['column_name'])
                    ->leftJoin($foreignKey['referenced_table_name'] . ' as t2', 't1.' . $foreignKey['column_name'], '=', 't2.' . $foreignKey['referenced_column_name'])
                    ->whereNull('t1.deleted_at')
                    ->whereNotNull('t2.deleted_at')
                    ->get(), $foreignKey['column_name']));
            }, $columnForeignKeys));
        }, array_combine($referencingTables, $referencingTables)));

        echo json_encode($missingForeignKeys, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;

        return 0;
    }
}
