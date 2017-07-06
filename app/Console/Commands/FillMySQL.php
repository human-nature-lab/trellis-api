<?php

namespace App\Console\Commands;

use DB;
use Faker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
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

        DB::setFetchMode(PDO::FETCH_ASSOC);

        $tables = $this->tables();
        $foreignKeys = $this->foreignKeys();
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
            return $this->columns($table);
        }, array_combine($tables, $tables));

        while (($sizeInBytes = $this->sizeInBytes()) < $this->argument('to_total_bytes')) {
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
    }

    /**
     * Returns the escaped form of an arbitrary string for safe usage directly in a SQL query (don't use this unless absolutely necessary).
    */
    public function escape($string)
    {
        return preg_replace('/[^0-9a-zA-Z_\.]/', '', $string);
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
     *     "id" => [
     *         "type" => "varchar(63)",
     *         "null" => "NO",
     *         "key" => "PRI",
     *         "default" => null,
     *         "extra" => "",
     *     ],
     * ]
     */
    public function columns($table)
    {
        $escapedTable = $this->escape($table);  // must escape table name in order to pass it directly to the MySQL "show columns" query

        return collect(array_map('array_change_key_case', DB::select("
            show columns from `$escapedTable`;
        ")))->groupBy('field')->map(function ($value) {
            return $value[0];
        })->toArray();

        // return DB::connection(config('database.default'))->getSchemaBuilder()->getColumnListing($table);
    }

    /**
     * Returns one of the following, where the length portion (in parentheses) has been stripped:
     *
     * bigint
     * blob
     * char
     * date
     * datetime
     * decimal
     * double
     * enum
     * float
     * int
     * longblob
     * longtext
     * mediumblob
     * mediumint
     * mediumtext
     * smallint
     * text
     * time
     * timestamp
     * tinyblob
     * tinyint
     * tinytext
     * varchar
     * year
     */
    public function unconstrainedType($type)
    {
        return strstr($type, '(', true) ?: $type;
    }

    /**
     * Returns the length portion (in parentheses) of a MySQL type or null if not present.
    */
    public function typeLength($type)
    {
        return ((int) trim(strstr($type, '('), '()')) ?: null;
    }

    /**
     * Returns fake data for integers, floats, strings and dates.
    */
    public function dataForType($type)//, $name = null)
    {
        $length = $this->typeLength($type);

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

        switch ($this->unconstrainedType($type)) {
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

    /**
     * Returns size of current database in bytes.
    */
    public function sizeInBytes()
    {
        return array_flatten(DB::select("
            select
                sum(data_length + index_length) 'size'
            from
                information_schema.tables
            where
                table_schema = ?;
        ", [config('database.connections.mysql.database')]))[0]*1;
    }
}
