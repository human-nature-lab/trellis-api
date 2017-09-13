<?php

namespace App\Console;

use App\Library\DatabaseHelper;
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
        \App\Console\Commands\CheckMySQLForeignKeys::class,
        \App\Console\Commands\CheckMySQLJSON::class,
        \App\Console\Commands\CheckMySQLSoftDeletes::class,
        \App\Console\Commands\CheckMySQLTriggersAndProcedures::class,
        \App\Console\Commands\ExportMySQL::class,
        \App\Console\Commands\ExportSnapshot::class,
        \App\Console\Commands\ExportSQLite::class,
        \App\Console\Commands\FillMySQL::class,
        \App\Console\Commands\ImportMySQL::class,
        \App\Console\Commands\ImportSnapshot::class,
        \App\Console\Commands\ImportSQLite::class,
        \App\Console\Commands\MergeMigrations::class,
        \App\Console\Commands\ShowMySQLJSON::class,
        \App\Console\Commands\ShowMySQLForeignKeys::class,
        \App\Console\Commands\ShowMySQLForeignKeyCycles::class,
        \App\Console\Commands\ShowMySQLTriggerCycles::class,
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
     * Handle an incoming console command.  NOTE handle() is only called from the CLI interface.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return int
     */
    public function handle($input, $output = null)
    {
        switch (array_get(\Request::server('argv', null), 1)) {
            case 'migrate':
            case 'migrate:refresh': // shame that this doesn't call 'php artisan migrate' internally
                $databaseConnection = config('database.default');
                $minDatabaseVersion = config("database.connections.$databaseConnection.version");

                if (version_compare(DatabaseHelper::version(), $minDatabaseVersion) < 0) {
                    dd("$databaseConnection $minDatabaseVersion is required.");
                }
                break;
        }

        return parent::handle($input, $output);
    }

    /**
     * @param InputInterface $request
     * @param int $response
     */
    public function terminate($request, $response)
    {
        switch ($request->getFirstArgument()) {
            case 'migrate':
            case 'migrate:refresh': // shame that this doesn't call 'php artisan migrate' internally
                $this->getArtisan()->call('trellis:check:mysql:json');

                echo PHP_EOL;

                $this->getArtisan()->call('trellis:check:mysql:triggersandprocedures');

                echo PHP_EOL;

                $this->getArtisan()->call('trellis:check:models');
                break;
        }

        // parent::terminate($request, $response);  // Laravel-only
    }
}
