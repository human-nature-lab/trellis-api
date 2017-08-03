<?php

namespace App\Console\Commands;

use App\Library\DatabaseHelper;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class CheckModels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trellis:check:models {--database= : Optional name of database to use} {--ignore-order : Ignore order of $fillable} {--include-migrations : Include `migrations` table (excluded by default)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compare the current database schema with every Model::$fillable';

    /**
     * Execute the console command.
     *
     * To call from within code:
     *
     * ob_start();
     *
     * \Illuminate\Support\Facades\Artisan::call('trellis:check:models');
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
            $tableColumns = collect(DatabaseHelper::tables())->flip()->map(function ($value, $table) {
                return collect(DatabaseHelper::columns($table))->map(function ($column) {
                    return DatabaseHelper::schemaJSON($column);
                });
            })->toArray();

            if (!$this->option('include-migrations')) {
                unset($tableColumns['migrations']);
            }

            $tableClasses = [];
            $output = '';
            $modelsDifferent = [];

            foreach (glob(base_path() . '/app/Models/*.php') as $filePath) {
                try {
                    $fileHandle = fopen($filePath, 'r');

                    if (!flock($fileHandle, LOCK_EX)) {
                        $this->error('Could not get exclusive lock to read $filePath.');

                        return 1;
                    }

                    $contents = stream_get_contents($fileHandle);
                    $class = '\\App\\Models\\' . head($this->getClasses($contents));
                    $model = new $class;
                    $table = $model->getTable();

                    if (!$this->option('include-migrations') && $table == 'migrations') {
                        continue;
                    }

                    $tableClasses[$table] = $class;

                    if (!isset($tableColumns[$table])) {
                        continue;
                    }

                    $columns = array_keys($tableColumns[$table]);
                    $contentsReplaced = preg_replace_callback('/([ \t]*)(public|protected|private)(\s)(\$fillable\s*=\s*\[)(.*?)(\])/s', function ($matches) use ($columns, $table) {
                        $columnsImploded = array_map(function ($column) use ($matches) {
                            return $matches[1] . $matches[1] . "'$column'";
                        }, $columns);

                        return $matches[1] . $matches[2] . $matches[3] . $matches[4] . "\n" . implode(",\n", $columnsImploded) . "\n" . $matches[1] . $matches[6];
                    }, $contents, 1);
                    $fillableGuarded = array_merge($model->getFillable(), array_filter($model->getGuarded(), function ($guarded) {
                        return $guarded != '*';
                    }));
                    $equal = $this->option('ignore-order') ? !count(array_diff($columns, $fillableGuarded)) : $columns == $fillableGuarded;

                    if (!$equal) {
                        $filePathEscaped = escapeshellarg($filePath);
                        $process = new Process(<<<EOT
diff -u $filePathEscaped - | sed 's/^-/\x1b[41m-/;s/^+/\x1b[42m+/;s/^@/\x1b[34m@/;s/$/\x1b[0m/'
EOT
                        , base_path());

                        $process->setInput($contentsReplaced)->setTimeout(null)->run();

                        $output .= $process->getOutput();

                        $modelsDifferent []= last(explode('\\', $class));
                    }
                } catch (\ReflectionException $e) {
                    $this->error("Class $class not found.  Always run `composer dump-autoload` before `php artisan {$this->signature}`.");  //NOTE get rid of this once figure out how to run autoload.php programmatically

                    die(1);
                } finally {
                    fclose($fileHandle);
                }
            }

            echo $output;

            if (count($modelsDifferent)) {
                echo json_decode('"\u274c"') . ' The following models have $fillable/$guarded that don\'t match their database tables: [' . implode(', ', $modelsDifferent) . ']' . PHP_EOL;
            } else {
                echo json_decode('"\u2714"') . ' All models have $fillable/$guarded that match their database tables.' . PHP_EOL;
            }

            $modelsWithoutTables = array_map(function ($class) {
                return last(explode('\\', $class));
            }, array_diff_key($tableClasses, $tableColumns));

            if (count($modelsWithoutTables)) {
                echo json_decode('"\u274c"') . ' The following models have no corresponding database tables: [' . implode(', ', $modelsWithoutTables) . ']' . PHP_EOL;
            } else {
                echo json_decode('"\u2714"') . ' All models have corresponding database tables.' . PHP_EOL;
            }

            $tablesWithoutModels = array_keys(array_diff_key($tableColumns, $tableClasses));

            if (count($tablesWithoutModels)) {
                echo json_decode('"\u274c"') . ' The following database tables have no corresponding models: [' . implode(', ', $tablesWithoutModels) . ']' . PHP_EOL;
            } else {
                echo json_decode('"\u2714"') . ' All database tables have corresponding models.' . PHP_EOL;
            }

            return 0;
        });
    }

    protected function getClasses($code)
    {
        $classes = [];
        $tokens = token_get_all($code);
        $count = count($tokens);

        for ($i = 2; $i < $count; $i++) {
            if ($tokens[$i - 2][0] == T_CLASS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING) {
                $class_name = $tokens[$i][1];
                $classes[] = $class_name;
            }
        }

        return $classes;
    }
}
