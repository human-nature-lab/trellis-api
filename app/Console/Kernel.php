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
    Commands\CheckModels::class,
    Commands\CheckMySQLForeignKeys::class,
    Commands\CheckMySQLJSON::class,
    Commands\CheckMySQLSoftDeletes::class,
    Commands\CheckMySQLTriggersAndProcedures::class,
    Commands\ExportMySQL::class,
    Commands\ExportSnapshot::class,
    Commands\ExportSnapshotV2::class,
    Commands\ExportSQLite::class,
    Commands\FillMySQL::class,
    Commands\ImportMySQL::class,
    Commands\ImportSnapshot::class,
    Commands\ImportSQLite::class,
    Commands\ImportUpload::class,
    Commands\MergeMigrations::class,
    Commands\MigrationsBegin::class,
    Commands\MigrationsEnd::class,
    Commands\ShowMySQLJSON::class,
    Commands\ShowMySQLForeignKeys::class,
    Commands\ShowMySQLForeignKeyCycles::class,
    Commands\ShowMySQLTriggerCycles::class,
    Commands\SimulateMigrate::class,
    Commands\SimulateMigrateRollback::class,
    Commands\ToTable::class,
    Commands\MakeReports::class,
    Commands\BundleLatestReports::class,
    Commands\DownloadApp::class,
    Commands\CheckDiskSpace::class,
    Commands\CleanReports::class,

    // overrides:   //TODO take this out if switched to Laravel
    Commands\MakeModel::class,
  ];

  /**
   * Register the commands for the application.
   *
   * @return void
   */
  protected function commands () {
    $this->load(__DIR__.'/Commands');
  }

  /**
   * Define the application's command schedule.
   *
   * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
   * @return void
   */
  protected function schedule (Schedule $schedule) {
    $schedule->command('trellis:check:disk-space --email=wyatt.israel@yale.edu --email=mark.mcknight@yale.edu')->dailyAt('08:00');
    $schedule->command('trellis:clean:reports --days-old=14')->dailyAt('07:00');
    $schedule->command('trellis:export:snapshotv2')->everyFiveMinutes();
    $schedule->command('trellis:import:upload')->everyMinute();
  }
}
