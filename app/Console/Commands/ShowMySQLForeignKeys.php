<?php

namespace App\Console\Commands;

use App\Library\DatabaseHelper;
use Illuminate\Console\Command;

class ShowMySQLForeignKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trellis:show:mysql:foreignkeys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show database foreign keys';

    /**
     * Execute the console command.
     *
     * To call from within code:
     *
     * ob_start();
     *
     * \Illuminate\Support\Facades\Artisan::call('trellis:show:mysql:foreignkeys');
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

        echo json_encode(DatabaseHelper::foreignKeys(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;

        return 0;
    }
}
