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
        \App\Console\Commands\CheckMySQL::class,
        \App\Console\Commands\ExportMySQL::class,
        \App\Console\Commands\ExportSnapshot::class,
        \App\Console\Commands\ExportSQLite::class,
        \App\Console\Commands\FillMySQL::class,
        \App\Console\Commands\ImportSQLite::class,
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
}
