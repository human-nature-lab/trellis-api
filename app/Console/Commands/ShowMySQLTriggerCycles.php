<?php

namespace App\Console\Commands;

use App\Library\DatabaseHelper;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use PDO;

class ShowMySQLTriggerCycles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trellis:show:mysql:triggercycles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show MySQL trigger cycles.  Prints JSON string of "[[t1, t2, ...], [t3, t4, ...], ...[t5, t6, ...]]" for any triggers that have cycles';

    /**
     * Execute the console command.
     *
     * To call from within code:
     *
     * ob_start();
     *
     * \Illuminate\Support\Facades\Artisan::call('trellis:show:mysql:triggercycles');
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

        ob_start();

        $this->call('trellis:show:mysql:foreignkeycycles');

        $traversalForeignKeys = json_decode(ob_get_clean(), true);
        $triggers = collect(DatabaseHelper::triggers())->keyBy('Trigger')->toArray();
        $traversalTriggerNames = [];

        foreach($traversalForeignKeys as $traversal => $foreignKeys) {
            $triggerNames = array_map(function ($foreignKey) use ($triggers) {
                $triggerName = DatabaseHelper::softDeleteTriggerName($foreignKey['table_name'], $foreignKey['column_name'], $foreignKey['referenced_table_name'], $foreignKey['referenced_column_name']);

                return isset($triggers[$triggerName]) ? $triggerName : null;
            }, $foreignKeys);

            if(count($foreignKeys) == count(array_filter($triggerNames))) {
                $traversalTriggerNames []= $triggerNames;
            }
        }

        echo json_encode($traversalTriggerNames, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;

        return 0;
    }
}
