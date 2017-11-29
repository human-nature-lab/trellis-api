<?php

namespace Database\MigrationsEnd;

use App\Library\DatabaseHelper;
use Illuminate\Console\Command;

class UpdateSoftDeleteTriggersAndProcedures extends Command
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
        DatabaseHelper::updateSoftDeleteTriggersAndProcedures();
    }
}
