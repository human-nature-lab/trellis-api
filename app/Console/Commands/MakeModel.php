<?php

namespace App\Console\Commands;

use App\Library\DatabaseHelper;
use DB;
use Faker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use PDO;

class MakeModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:model {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Placeholder command for `php artisan make:migration:json --file=schema.json` to allow running with Lumen';

    /**
     * Execute the console command.
     *
     * @return integer
     */
    public function handle()
    {
        echo 'The `php artisan make:model {name}` command is not part of Lumen, but app/Console/Commands/MakeModel.php is provided as a stub so commands which depend upon it can run.' . PHP_EOL;

        return 0;
    }
}
