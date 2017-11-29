<?php

namespace Database\MigrationsEnd;

use Illuminate\Console\Command;

class CheckDatabaseModels extends Command
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
        $this->call('trellis:check:mysql:json');

        echo PHP_EOL;

        $this->call('trellis:check:mysql:triggersandprocedures');

        echo PHP_EOL;

        $this->call('trellis:check:models');

        return 0;
    }
}
