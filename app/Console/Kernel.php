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

        // overrides:   //TODO take this out if switched to Laravel
        Commands\MakeModel::class,
    ];

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }

  /**
   * Define the application's command schedule.
   *
   * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
   * @return void
   */
  protected function schedule (Schedule $schedule) {
    if (env('CHECK_DISK_SPACE', 1)) {
      $checkDiskTime = env('CHECK_DISK_SPACE_TIME', '08:00');
      $emailString = env('DISK_LOW_EMAILS', 'wyatt.israel@yale.edu,mark.mcknight@yale.edu');
      $emails = explode(',', $emailString);
      $cmd = 'trellis:check:disk-space';
      foreach ($emails as $email) {
        $cmd .= ' --email=' . $email;
      }
      $schedule->command($cmd)->dailyAt($checkDiskTime);
    }
    if (env('CLEAN_REPORTS', 1)) {
      $cleanReportsTime = env('CLEAN_REPORTS_TIME', '07:00');
      $cleanReportsOlderThan = env('CLEAN_REPORTS_AGE', '14');
      $schedule->command('trellis:clean:reports --days-old=' . $cleanReportsOlderThan)->dailyAt($cleanReportsTime);
    }
    if (env('EXPORT_SNAPSHOTS', 0)) {
      $schedule->command('trellis:export:snapshotv2')->everyFiveMinutes();
    }
    if (env('IMPORT_UPLOADS', 0)) {
      $schedule->command('trellis:import:upload')->everyMinute();
    }

    if (env('GENERATE_REPORTS', 0)) {
      $reportTime = env('GENERATE_REPORTS_TIME', '00:00');
      $schedule->command('trellis:make:reports')->dailyAt($reportTime);
    }

  }
}
