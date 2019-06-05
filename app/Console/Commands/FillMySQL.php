<?php

namespace App\Console\Commands;

use App\Library\DatabaseHelper;
use DB;
use Faker;
use Illuminate\Console\Command;
use Illuminate\Database\Events\StatementPrepared;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use PDO;

class FillMySQL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trellis:fill:mysql {to_total_bytes} {skipping_tables?*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill MySQL database with fake data (ex: 100000 table_to_skip another_table_to_skip ...)';

    /**
     * The console command description.
     *
     * @var Faker\Generator
     */
    protected $faker;

    /**
     * The console command description.
     *
     * @var Faker\Guesser\Name
     */
    // protected $guesser;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->faker = Faker\Factory::create();
        // $this->guesser = new Faker\Guesser\Name($this->faker);

        if (!env('APP_DEBUG') || env('APP_ENV') != 'dev') {
            $this->error('Can only run `php artisan ' . $this->signature . '` in local dev environment.');

            return 1;
        }

        if (config('database.default') != 'mysql') {
            $this->error('Currently `php artisan ' . $this->signature . '` only works with MySQL.');

            return 1;
        }

        // Must listen for event to change the fetch mode now
        Event::listen(StatementPrepared::class, function ($event) {
            $event->statement->setFetchMode(PDO::FETCH_ASSOC);
        });

        $tables = DatabaseHelper::tables();
        $foreignKeys = DatabaseHelper::foreignKeys();
        $tableColumnForeignKeys = array_merge(array_combine($tables, array_fill(0, count($tables), [])), collect($foreignKeys)->groupBy('table_name')->map(function ($attributes) {
            return collect($attributes)->keyBy('column_name')->toArray();
        })->toArray()); // keys contain every table but some values are an empty array if no columns are foreign keys
        $referencingTables = array_values(array_sort(array_unique(array_column($foreignKeys, 'table_name')), function ($value) {
            return $value;
        }));
        $referencedTables = array_values(array_sort(array_unique(array_column($foreignKeys, 'referenced_table_name')), function ($value) {
            return $value;
        }));
        $independentTables = array_diff($tables, $referencingTables);
        $tableColumns = array_map(function ($table) {
            return DatabaseHelper::columns($table);
        }, array_combine($tables, $tables));

        while (($sizeInBytes = DatabaseHelper::sizeInBytes()) < $this->argument('to_total_bytes')) {
            // populate any tables that don't contain foreign keys
            foreach ($independentTables as $independentTable) {
                if (in_array($independentTable, $this->argument('skipping_tables'))) {
                    continue;   // skip undesired tables
                }

                $columnAttributes = $tableColumns[$independentTable];
                $data = array_map(function ($attributes) {
                    return $this->dataForType($attributes['type']);//, $attributes['field']);
                }, $columnAttributes);

                try {
                    DB::table($independentTable)->insert($data);
                } catch (\Illuminate\Database\QueryException $e) {
                    // ignore duplicate keys for now and just try again on next pass
                }
            }

            // populate any tables that contain foreign keys to other tables
            foreach ($referencingTables as $referencingTable) {
                if (in_array($referencingTable, $this->argument('skipping_tables'))) {
                    continue;   // skip undesired tables
                }

                $columnAttributes = $tableColumns[$referencingTable];
                $columnForeignKeys = $tableColumnForeignKeys[$referencingTable];

                $data = array_map(function ($attributes) use ($columnForeignKeys) {
                    $column = $attributes['field'];

                    if (($foreignKey = array_get($columnForeignKeys, $column)) != null) {   // if column is a foreign key then look up random row in referenced table
                        $referencedRow = DB::table($foreignKey['referenced_table_name'])->select($foreignKey['referenced_column_name'])->orderByRaw('rand()')->first();

                        return array_get($referencedRow, $foreignKey['referenced_column_name']); // may be null if referenced table is empty
                    } else {    // otherwise populate column with fake data (same as for independent tables)
                        return $this->dataForType($attributes['type']);//, $attributes['field']);
                    }
                }, $columnAttributes);

                try {
                    DB::table($referencingTable)->insert($data);
                } catch (\Illuminate\Database\QueryException $e) {
                    // if ($e->getCode() != 23000) {  // ignore foreign key errors for now but report all others (it takes a few passes for tables to populate before others can reference them)
                    //     throw $e;
                    // }
                }
            }

            $this->info($sizeInBytes);
        }

        $this->info('Filled MySQL database with fake data.  Total size = ' . $sizeInBytes . ' bytes.');

        return 0;
    }

    /**
     * Returns fake data for integers, floats, strings and dates.
    */
    public function dataForType($type)//, $name = null)
    {
        $length = DatabaseHelper::typeLength($type);

        if ($length > 255) {
            $length = 255;  // if length is not null, limit to 255 for now
        }

        // if ($name) {
        //     $closure = $this->guesser->guessFormat($name, $length);
        //
        //     if ($closure) {
        //         $guess = $closure();
        //
        //         if ($guess) {
        //             return $guess;
        //         }
        //     }
        // }

        switch (DatabaseHelper::unconstrainedType($type)) {
            case 'bigint':
            case 'int':
            case 'mediumint':
            case 'smallint':
            case 'tinyint':
                return $this->faker->randomNumber();

            case 'decimal':
            case 'double':
            case 'float':
                return $this->faker->randomFloat();

            case 'blob':
            case 'char':
            case 'longblob':
            case 'longtext':
            case 'mediumblob':
            case 'mediumtext':
            case 'text':
            case 'tinyblob':
            case 'tinytext':
            case 'varchar':
                return $this->faker->text($length ?: 255);

            case 'date':
            case 'datetime':
            case 'time':
            case 'timestamp':
            case 'year':
                return $this->faker->dateTime();

            case 'enum':
            default:
                return $this->faker->text($length ?: 255);
        }
    }
}
