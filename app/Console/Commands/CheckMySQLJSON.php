<?php

namespace App\Console\Commands;

use App\Library\DatabaseHelper;
use DB;
use Faker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use PDO;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class CheckMySQLJSON extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trellis:check:mysql:json {--database= : Optional name of database to use}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compare schema.json with the current database schema.  Prints JSON with differences highlighted';

    /**
     * Execute the console command.
     *
     * To call from within code:
     *
     * ob_start();
     *
     * \Illuminate\Support\Facades\Artisan::call('trellis:check:mysql:json');
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
            $fileSchemaHandle = fopen(base_path() . '/schema.json', 'r');

            if (!flock($fileSchemaHandle, LOCK_EX)) {
                $this->error('Could not get exclusive lock to read schema.json.');

                return 1;
            }

            $fileSchemaContents = stream_get_contents($fileSchemaHandle);
            $fileSchemaArray = json_decode($fileSchemaContents, true);

            ob_start();

            $this->call('trellis:show:mysql:json');//php artisan trellis:show:mysql:json

            $dbSchemaContents = ob_get_clean();
            $dbSchemaArray = json_decode($dbSchemaContents, true);

            echo PHP_EOL;

            if ($fileSchemaArray == $dbSchemaArray) {
                echo json_decode('"\u2714"') . ' schema.json and the current database schema are identical.' . PHP_EOL;
            } else {
                $process = new Process(<<<EOT
diff -U-1 schema.json - | sed 's/^-/\x1b[41m-/;s/^+/\x1b[42m+/;s/^@/\x1b[34m@/;s/$/\x1b[0m/'
EOT
                , base_path());

                $process->setInput($dbSchemaContents)->setTimeout(null)->run();

                fclose($fileSchemaHandle);

                $diff = $process->getOutput();
                $tablesOnlyInFile = array_keys(array_diff_key($fileSchemaArray, $dbSchemaArray));
                $tablesOnlyInDB = array_keys(array_diff_key($dbSchemaArray, $fileSchemaArray));
                $tablesModified = array_diff(array_keys(array_udiff_assoc($fileSchemaArray, $dbSchemaArray, function ($a, $b) {
                    return $a != $b;
                })), $tablesOnlyInFile, $tablesOnlyInDB);
                $tablesAddedOrRemoved = array_unique(array_merge($tablesOnlyInFile, $tablesOnlyInDB));

                asort($tablesAddedOrRemoved);

                echo 'schema.json and the current database schema differ:' . PHP_EOL . PHP_EOL;

                echo $diff . PHP_EOL;

                echo 'Tables only in schema.json: ' . implode(', ', $tablesOnlyInFile) . PHP_EOL;
                echo 'Tables only in the database: ' . implode(', ', $tablesOnlyInDB) . PHP_EOL;
                echo 'Tables updated: ' . implode(', ', $tablesModified) . PHP_EOL;

                $dbSimulated = escapeshellarg(config('database.connections.mysql.database') . '_simulated');

                echo <<<EOT

==================================================
TO MAKE SCHEMA.JSON AND THE DATABASE SCHEMA MATCH:
==================================================

(1) See:

https://laravel.com/docs/migrations
https://github.com/mojopollo/laravel-json-schema
https://github.com/laracasts/Laravel-5-Generators-Extended

(2) Backup your database to a SQL dump.

(3) Edit schema.json using the syntax from Laravel Generators (optional).

(4) Create migrations similar to the following examples:

php artisan make:migration create_my_table
php artisan make:migration add_my_column_to_my_table
php artisan make:migration remove_my_column_from_my_table
php artisan make:migration change_my_column_in_my_table

Which saves files similar to:

database/migrations/XXXX_XX_XX_XXXXXX_create_my_table
database/migrations/XXXX_XX_XX_XXXXXX_add_my_column_to_my_table
database/migrations/XXXX_XX_XX_XXXXXX_remove_my_column_from_my_table
database/migrations/XXXX_XX_XX_XXXXXX_change_my_column_in_my_table

Add code to the migrations to update the schema.

(5) Simulate the new schema in a temporary $dbSimulated database using:

composer dump-autoload && php artisan trellis:simulate:migrate --preserve && php artisan trellis:check:mysql:json --database=$dbSimulated

(6) Once migrations have been updated to your satisfaction, export the simulated database schema:

cat schema.json > "schema_$(date +%Y_%m_%d_%H%M%S).json" && composer dump-autoload && php artisan trellis:simulate:migrate --preserve && php artisan trellis:show:mysql:json --database=$dbSimulated > schema.json

(7) Next time you run `php artisan migrate` you should see: "schema.json and the current database schema are identical."

EOT;

                // //TODO add shell script examples to add/remove/modify tables/rows using functionality from https://github.com/mojopollo/laravel-json-schema and https://github.com/laracasts/Laravel-5-Generators-Extended
                //
                // ///// schema.json to database /////
                //
                // echo 'To convert schema.json to the current database schema:' . PHP_EOL . PHP_EOL;
                //
                // ///// database to schema.json /////
                //
                // echo 'To convert the current database schema to schema.json:' . PHP_EOL . PHP_EOL;
                //
                // return 0;
                // ///////////////////
                //
                // echo 'Please update the database schema, update schema.json or add migrations until they match.  For example:' . PHP_EOL;
                //
                // if (count($tablesAddedOrRemoved)) {
                //     echo PHP_EOL . 'To add tables:' . PHP_EOL;
                //
                //     foreach ($tablesAddedOrRemoved as $table) {
                //         $escapedTable = DatabaseHelper::escape($table, false);
                //
                //         echo "php artisan make:migration:json --file=schema.json --disableundo --only=$escapedTable" . PHP_EOL;
                //     }
                //
                //     echo PHP_EOL . 'To remove tables:' . PHP_EOL;
                //
                //     foreach ($tablesAddedOrRemoved as $table) {
                //         $escapedTable = DatabaseHelper::escape($table, false);
                //
                //         echo 'php artisan make:migration drop_' . $escapedTable . '_table' . ' # <- then set the contents of up() to: ' . "Schema::drop('$escapedTable');" . PHP_EOL;
                //     }
                // }
                //
                // if (count($tablesModified)) {
                //     echo PHP_EOL . 'To add columns:' . PHP_EOL;
                //
                //     foreach ($tablesModified as $table) {
                //         $fileColumns = array_keys($fileSchemaArray[$table]);
                //         $dbColumns = array_keys($dbSchemaArray[$table]);
                //         $columnsOnlyInFile = array_values(array_diff($fileColumns, $dbColumns));
                //         $columnsOnlyInDB = array_values(array_diff($dbColumns, $fileColumns));
                //         $columnsModified = array_diff(array_keys(array_udiff_assoc($fileSchemaArray[$table], $dbSchemaArray[$table], function ($a, $b) {
                //             return $a != $b;
                //         })), $columnsOnlyInFile, $columnsOnlyInDB);
                //         $columnsAddedOrRemoved = array_unique(array_merge($columnsOnlyInFile, $columnsOnlyInDB));
                //
                //         asort($columnsAddedOrRemoved);
                //
                //         $escapedTable = DatabaseHelper::escape($table, false);
                //
                //         // dump($fileColumns, $dbColumns, $columnsOnlyInFile, $columnsOnlyInDB, $columnsModified, $columnsAddedOrRemoved);
                //
                //         foreach ($columnsAddedOrRemoved as $column) {
                //             foreach ([$fileSchemaArray, $dbSchemaArray] as $schemaArray) {
                //                 $escapedColumn = DatabaseHelper::escape($column, false);
                //                 $schema = array_get($schemaArray, "$table.$column");
                //
                //                 if ($schema) {
                //                     echo "php artisan make:migration:schema --model=false add_{$escapedColumn}_to_{$escapedTable}_table --schema=\"$column:$schema\"" . PHP_EOL;
                //                 }
                //             }
                //         }
                //         // echo "php artisan make:migration:schema --model=false remove_{$table}_table --schema=\"\""
                //
                //         //php artisan make:migration:schema remove_user_id_from_posts_table --schema="user_id:integer"
                //     }
                //
                //     echo PHP_EOL . 'If columns have been modified, see https://laravel.com/docs/migrations#modifying-columns for more details.' . PHP_EOL;
                // }
            }

            return 0;
        });
    }
}
