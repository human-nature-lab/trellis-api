<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\CheckModels::class,
        \App\Console\Commands\CheckMySQL::class,
        \App\Console\Commands\CheckMySQLJSON::class,
        \App\Console\Commands\ExportMySQL::class,
        \App\Console\Commands\ExportSnapshot::class,
        \App\Console\Commands\ExportSQLite::class,
        \App\Console\Commands\FillMySQL::class,
        \App\Console\Commands\ImportSQLite::class,
        \App\Console\Commands\MergeMigrations::class,
        \App\Console\Commands\ShowMySQLJSON::class,
        \App\Console\Commands\ShowForeignKeys::class,
        \App\Console\Commands\SimulateMigrate::class,
        \App\Console\Commands\SimulateMigrateRollback::class,
        \App\Console\Commands\ToTable::class,

        // overrides:   //TODO take this out if switched to Laravel
        \App\Console\Commands\MakeModel::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }

    /**
     * @param InputInterface $request
     * @param int $response
     */
    public function terminate($request, $response)
    {
        switch ($request->getFirstArgument()) {
            case 'migrate':
                $this->getArtisan()->call('trellis:check:mysql:json');

                echo PHP_EOL;

                $this->getArtisan()->call('trellis:check:models');
                break;
        }

        // parent::terminate($request, $response);  // Laravel-only
    }
}
