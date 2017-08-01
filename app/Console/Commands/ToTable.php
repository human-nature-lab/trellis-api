<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ToTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trellis:to:table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert JSON string from STDIN to ASCII table format';

    /**
     * Execute the console command.
     *
     * To call from within code:
     *
     * ob_start();
     *
     * \Illuminate\Support\Facades\Artisan::call('trellis:to:table');
     *
     * $result = json_decode(ob_get_clean(), true);
     *
     * @return mixed
     */
    public function handle()
    {
        $this->printTable(json_decode(stream_get_contents(STDIN), true));

        return 0;
    }

    /**
    * Print a two-dimensional array of key-value pairs to stdout.
    *
    * @param   array  $array  Two-dimensional array of key-value pairs to print
    * @return  null
    **/
    public function printTable($array)
    {
        $columns = array_keys(array_get($array, 0, []));
        $widths = array_map(function ($column) use ($array) {
            return max(array_map('strlen', array_merge(array_column($array, $column), [$column])));
        }, $columns);
        $separators = array_map(function ($column) {
            return str_pad('', $column, '-');
        }, $widths);

        foreach (array_merge([$columns], [$separators], $array) as $row) {
            echo implode('|', array_map(function ($column, $field) use ($widths) {
                return str_pad($field, isset($widths[$column]) ? $widths[$column] : 0);
            }, array_keys($columns), $row)) . PHP_EOL;
        }
    }
}
