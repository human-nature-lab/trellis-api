<?php

namespace App\Console\Commands;

use App\Library\DatabaseHelper;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use PDO;

/*

# this SQL can be imported into a blank database to demonstrate foreign key traversal

DROP TABLE IF EXISTS `human`;

CREATE TABLE `human` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `c` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`c`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# ------------------------------------------------------------

DROP TABLE IF EXISTS `dog`;

CREATE TABLE `dog` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `b` int(11) DEFAULT NULL,
  `e` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`b`),
  UNIQUE KEY (`e`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# ------------------------------------------------------------

DROP TABLE IF EXISTS `cat`;

CREATE TABLE `cat` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `a` int(11) DEFAULT NULL,
  `d` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`a`),
  UNIQUE KEY (`d`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# ------------------------------------------------------------

ALTER TABLE `human` ADD CONSTRAINT `c` FOREIGN KEY (`c`) REFERENCES `cat` (`a`);
ALTER TABLE `dog` ADD CONSTRAINT `b` FOREIGN KEY (`b`) REFERENCES `human` (`c`);
ALTER TABLE `dog` ADD CONSTRAINT `e` FOREIGN KEY (`e`) REFERENCES `cat` (`d`);
ALTER TABLE `cat` ADD CONSTRAINT `a` FOREIGN KEY (`a`) REFERENCES `dog` (`b`);
ALTER TABLE `cat` ADD CONSTRAINT `d` FOREIGN KEY (`d`) REFERENCES `dog` (`e`);

*/

class ShowMySQLForeignKeyCycles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trellis:show:mysql:foreignkeycycles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show MySQL foreign key cycles.  Prints JSON string of "[[fk1, fk2, ...], [fk3, fk4, ...], ...[fk5, fk6, ...]]" for any foreign keys that have cycles';

    /**
     * Execute the console command.
     *
     * To call from within code:
     *
     * ob_start();
     *
     * \Illuminate\Support\Facades\Artisan::call('trellis:show:mysql:foreignkeycycles');
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
        $tableColumnTraversals = [];

        foreach($tableColumnForeignKeys as $table => $columnForeignKeys) {
            foreach($columnForeignKeys as $column => $foreignKeys) {
                if(!$this->traverseForeignKeys($tableColumnForeignKeys, $table, $column, $table, [], $tableColumnTraversals)) {
                    unset($tableColumnTraversals[$table][$column]); // remove non-cyclical traversals
                }
            }
        }

        $tableColumnTraversals = array_filter($tableColumnTraversals);  // filter out traversals that have no cycles
        $traversalForeignKeys = array_map(function ($traversal) use ($tableColumnForeignKeys) {
            return array_filter(array_map(function ($tableColumn) use ($tableColumnForeignKeys) {
                return isset($tableColumn[1]) ? $tableColumnForeignKeys[$tableColumn[0]][$tableColumn[1]] : null;
            }, array_chunk($traversal, 2)));
        }, $this->traversal($tableColumnTraversals));

        echo json_encode($traversalForeignKeys, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;

        return 0;
    }

    function traverseForeignKeys($tableColumnForeignKeys, $table, $column, $findTable, $seen, &$result)
    {
        if(!isset($result[$table])) {
            $result[$table] = [];
        }

        if(!isset($result[$table][$column])) {
            $result[$table][$column] = [];
        }

        $result = &$result[$table][$column];

        if(!isset($tableColumnForeignKeys[$table][$column]) || isset($seen[$table][$column])) {
            return false;   // end of traversal reached or detected infinite loop
        }

        if(!isset($seen[$table])) {
            $seen[$table] = [];
        }

        $seen[$table][$column] = true;

        $foreignKey = $tableColumnForeignKeys[$table][$column];
        $nextTable = $foreignKey['referenced_table_name'];
        $nextColumn = $foreignKey['referenced_column_name'];

        if($nextTable == $findTable) {
            $result = $findTable;

            return true;
        }

        return $this->traverseForeignKeys($tableColumnForeignKeys, $nextTable, $nextColumn, $findTable, $seen, $result);
    }

    // similar to array_dot() and Arr:dot() but returns traversals as arrays instead of strings
    protected function traversal($array, $prepend = [])
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && ! empty($value)) {
                $results = array_merge($results, $this->traversal($value, array_merge($prepend, [$key])));
            } else {
                $results []= array_merge($prepend, [$key], [$value]);
            }
        }

        return $results;
    }
}
