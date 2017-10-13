<?php

namespace Database\MigrationsBegin;

use App\Library\DatabaseHelper;
use Illuminate\Console\Command;

class CheckDatabaseVersion extends Command
{
    // /**
    //  * The name and signature of the console command.
    //  *
    //  * @var string
    //  */
    protected $signature = 'Not registered in app/Console/Kernel.php';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $databaseConnection = config('database.default');
        $minDatabaseVersion = config("database.connections.$databaseConnection.version");

        if (version_compare(DatabaseHelper::version(), $minDatabaseVersion) < 0) {
            dd("$databaseConnection $minDatabaseVersion is required.");
        }

        return 0;
    }
}
