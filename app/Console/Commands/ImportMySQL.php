<?php

namespace App\Console\Commands;

use App\Library\DatabaseHelper;
use App\Library\FileHelper;
use App\Library\TimeHelper;
use App\Models\Log;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminated\Console\WithoutOverlapping;
use PhpMyAdmin\SqlParser\Parser;
use PhpMyAdmin\SqlParser\Statements\InsertStatement;
use PhpMyAdmin\SqlParser\Token;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ImportMySQL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trellis:import:mysql {--exclude=*} {storage_path?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import MySQL database (currently INSERT statements only) from storage_path (or stdout if not specified). --exclude=<table> can be specified multiple times to exclude table(s) from the import. Returns either 1) the number of rows modified and exit code 0 or 2) the output of `php artisan trellis:check:mysql:foreignkeys` and exit code 1';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tableColumnTypes = collect(DatabaseHelper::tables())->flip()->map(function ($index, $table) {
            return array_map(function ($attributes) {
                return $attributes['type'];
            }, DatabaseHelper::columns($table));
        })->toArray();

        app()->configure('snapshot');   // save overhead by only loading config when needed

        $stdin = fopen(is_null($this->argument('storage_path')) ? (\App::runningInConsole() ? 'php://stdin' : 'php://input') : $this->argument('storage_path'), 'rb');
        $characters = '';
        // $totalTableRows = [];    //NOTE if any problems are encountered during high contention due to row locking, then append $tableRows to $totalTableRows and retry transaction with it up to some number of times
        $totalWrites = 0;

        // $writes = [
        //     'created' => 0,
        //     'updated' => 0,
        //     'deleted' => 0,
        //     'logged' => 0,
        //     'skipped' => 0,
        // ];

        DB::beginTransaction();

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        do {
            $chunk = stream_get_line($stdin, 0);   // same as fread() when no delimiter specified, but defaults to at least 8192 bytes per read

            if (feof($stdin)) {
                $chunk .= ";\n";  // terminate stream with semicolon-newline in case client did not
            }

            $characters .= $chunk;

            $parser = new Parser($characters);
            $lastSemicolon = array_slice(array_filter($parser->list->tokens, function ($token) {
                return $token->type == Token::TYPE_DELIMITER && isset($token->position);
            }), -1, 1, true);

            if (count($lastSemicolon)) {
                $lastSemicolonIndex = key($lastSemicolon);
                $lastSemicolonPosition = last($lastSemicolon)->position;
                $statements = array_filter($parser->statements, function ($statement) use ($lastSemicolonIndex) {
                    return $statement->last < $lastSemicolonIndex;  // semicolon is never part of statement's tokens (it's the next token)
                });
                $insertStatements = $this->getInsertStatements($statements);
                $tableRows = $this->insertsToTableRows($insertStatements, $tableColumnTypes);

                // //NOTE if any problems are encountered during high contention due to row locking, then append $tableRows to $totalTableRows and retry transaction with it up to some number of times
                // foreach ($tableRows as $table => $rows) {
                //     if (!isset($totalTableRows[$table])) {
                //         $totalTableRows[$table] = [];
                //     }
                //
                //     foreach ($rows as $row) {
                //         $totalTableRows[$table] []= $row;
                //     }
                // }

                foreach ($tableRows as $table => $rows) {
                    foreach ($rows as $row) {
                        $result = Log::writeRow($row, $table);

                        if (isset($result)) {
                            $totalWrites++;

                            // switch ($result) {
                            //     case 'create':
                            //         $writes['created']++;
                            //         break;
                            //
                            //     case 'update':
                            //         $writes['updated']++;
                            //         break;
                            //
                            //     case 'delete':
                            //         $writes['deleted']++;
                            //         break;
                            //
                            //     default:
                            //         $writes['logged']++;
                            //         break;
                            // }
                        } else {
                            // $writes['skipped']++;
                        }
                    }
                }

                $characters = mb_substr($characters, $lastSemicolonPosition + 1, null, 'UTF-8');    // advance to the character after the last semicolon parsed.  note that PhpMyAdmin\SqlParser uses character offsets instead of byte offsets
            }
        } while (!feof($stdin));

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        ob_start();

        Artisan::call('trellis:check:mysql:foreignkeys');

        $inconsistencies = ob_get_clean();

        if (count(json_decode($inconsistencies, true))) {
            echo $inconsistencies;

            DB::rollBack();

            return 1;
        }

        echo $totalWrites . PHP_EOL;

        DB::commit();   //NOTE if any problems are encountered during high contention due to row locking, then append $tableRows to $totalTableRows and retry transaction with it up to some number of times

        return 0;
    }

    /**
     * Given an array of PhpMyAdmin\SqlParser\Parser InsertStatement and a table => column => type array, returns array having form of [table => [[column => value], ...]].
     *
     * @param  array $inserts          Array of PhpMyAdmin\SqlParser\Parser InsertStatement
     * @param  array $tableColumnTypes Array having form of [table => column => type]
     * @return null
     */
    protected function insertsToTableRows($inserts, $tableColumnTypes)
    {
        $tableRows = [];

        $substitutions = config('snapshot.substitutions.upload');   //TODO this should probably be handled by ImportSnapshot passing --exclude=table1,table2,... but would need to figure out syntax for passing substitutions

        foreach ($this->option('exclude') as $exclude) {
            $substitutions[$exclude] = [
                '*' => null,
            ];
        }

        foreach ($inserts as $insert) {
            $table = $insert->into->dest->table;

            if (!$table || !count($insert->into->columns)) {
                continue;   // require "insert into `table` (`field`) values ('value')" syntax where fields are specified
            }

            if (!isset($tableColumnTypes[$table])) {
                continue;   // skip unknown tables  //TODO make this a whitelist
            }

            if (!isset($tableRows[$table])) {
                $tableRows[$table] = [];
            }

            $fields = $insert->into->columns;
            $values = $insert->values[0]->values;

            foreach ($insert->values[0]->raw as $key => $raw) {
                if (strtolower($raw) == 'null') {
                    $values[$key] = null;   // fix issue where null is parsed as "null" instead of null
                }
            }

            $fieldValues = array_combine($fields, $values);

            // skip any blacklisted fields
            foreach (array_get($substitutions, $table, []) as $field => $substitution) {
                if ($field == '*') {
                    $fieldValues = array_fill_keys(array_keys($fieldValues), $substitution);  // if wildcard, substitute all fields
                } else {
                    if(isset($substitution)) {
                        $fieldValues[$field] = $substitution;
                    } else
                        unset($fieldValues[$field]);    // skip field altogether if substitution is null
                }
            }

            foreach ($fieldValues as $field => $value) {
                if (!is_null($value)) {
                    if (in_array(array_get($tableColumnTypes[$table], $field), ['date', 'datetime', 'time', 'timestamp', 'year'])) {
                        $fieldValues[$field] = TimeHelper::utc($value); // ensure that timestamp/datetime is formatted properly for insertion
                    }
                }
            }

            if (count($fieldValues)) {
                $tableRows[$table] []= $fieldValues;
            }
        }

        return $tableRows;
    }

    /**
     * Given an array of PhpMyAdmin\SqlParser\Parser statements, extract the INSERT statements and return them.  handles nested statements (within transactions for example).
     *
     * @param  array $statements Array of PhpMyAdmin\SqlParser\Parser statements
     * @return array Array of PhpMyAdmin\SqlParser\Parser InsertStatement
     */
    protected function getInsertStatements($statements)
    {
        $inserts = [];

        foreach ($statements as $statement) {
            if (get_class($statement) == InsertStatement::class) {
                $inserts []= $statement;
            }

            if (isset($statement->statements)) {
                foreach ($this->getInsertStatements($statement->statements) as $insertStatement) {
                    $inserts []= $insertStatement;
                }
            }
        }

        return $inserts;
    }
}
