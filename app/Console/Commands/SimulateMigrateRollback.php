<?php

namespace App\Console\Commands;

use App\Library\DatabaseHelper;
use DB;
use Faker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use PDO;

class SimulateMigrateRollback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trellis:simulate:migrate:rollback';

    /**
     * The console command description.
     *
     * @var string
     */
    // protected $description = 'Simulates migrating database from scratch in simulated database, preserving it if specified';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->description = 'Simulates rolling back migration in ' . DatabaseHelper::escape(config('database.connections.mysql_simulated.database')) . ' database';

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return integer
     */
    public function handle()
    {
        if (!env('APP_DEBUG') || env('APP_ENV') != 'dev') {
            $this->error('Can only run `php artisan ' . $this->signature . '` in local dev environment.');

            return 1;
        }

        if (config('database.default') != 'mysql') {
            $this->error('Currently `php artisan ' . $this->signature . '` only works with MySQL.');

            return 1;
        }

        $database = DatabaseHelper::escape(config('database.connections.mysql_simulated.database'));

        try {
            DatabaseHelper::useDatabase($database, function () use ($database) {
                echo "Using database $database." . PHP_EOL;

                if ($this->call('migrate:rollback', [
                    '--database' => 'mysql_simulated'   // use the mysql_simulated configuration (not a database with the specified name)
                ]) != 0) {
                    $this->error("Sorry, an unkown error occurred while trying to run migrations in $database.");

                    return 1;
                }
            });
        } catch (\Exception $e) {
            throw $e;
        } finally {
        }
    }
}
