<?php

namespace App\Console\Commands;

use App\Library\DatabaseHelper;
use DB;
use Faker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use PDO;

class CheckMySQLTriggersAndProcedures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trellis:check:mysql:triggersandprocedures';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check MySQL triggers and procedures.  Find any foreign keys without corresponding triggers/procedures and vice versa.';

    /**
     * Execute the console command.
     *
     * To call from within code:
     *
     * ob_start();
     *
     * \Illuminate\Support\Facades\Artisan::call('trellis:check:mysql:triggersandprocedures');
     *
     * $result = json_decode(ob_get_clean(), true);
     *
     * @return mixed
     */
    public function handle()
    {
        if (config('database.default') != 'mysql') {
            $this->error('Currently `php artisan ' . $this->signature . '` only works with MySQL.');

            return 1;
        }

        DB::setFetchMode(PDO::FETCH_ASSOC);

        $foreignKeys = DatabaseHelper::foreignKeys();
        $triggers = DatabaseHelper::softDeleteTriggers();
        $procedure = DatabaseHelper::softDeleteProcedureName();
        $procedureForeignKeys = DatabaseHelper::softDeleteProcedureForeignKeys($procedure);
        $success = true;

        foreach($foreignKeys as $foreignKey) {
            $trigger = DatabaseHelper::softDeleteTriggerName($foreignKey['table_name'], $foreignKey['column_name'], $foreignKey['referenced_table_name'], $foreignKey['referenced_column_name']);
            $foreignKeyTableColumns = array_intersect_key($foreignKey, array_flip(['table_name', 'column_name', 'referenced_table_name', 'referenced_column_name']));

            if(!in_array($trigger, $triggers) && !in_array($foreignKeyTableColumns, $procedureForeignKeys)) {
                echo "Soft delete foreign key `{$foreignKey['constraint_name']}` is not present in any soft delete triggers or procedures." . PHP_EOL;

                $success = false;
            }
        }

        foreach($triggers as $trigger) {
            $foreignKey = head(DatabaseHelper::softDeleteTriggerForeignKey($trigger)) ?: null;

            if(is_null($foreignKey)) {
                echo "Soft delete trigger `$trigger` does not have a corresponding foreign key." . PHP_EOL;

                $success = false;
            }
        }

        foreach($procedureForeignKeys as $procedureForeignKey) {
            $matches = array_filter($foreignKeys, function ($foreignKey) use ($procedureForeignKey) {
                return $foreignKey['table_name'] == array_get($procedureForeignKey, 'table_name') &&
                    $foreignKey['column_name'] == array_get($procedureForeignKey, 'column_name') &&
                    $foreignKey['referenced_table_name'] == array_get($procedureForeignKey, 'referenced_table_name') &&
                    $foreignKey['referenced_column_name'] == array_get($procedureForeignKey, 'referenced_column_name');
            });

            if(!count($matches)) {
                echo "Soft delete procedure `$procedure` does not have a corresponding foreign key from `{$procedureForeignKey['table_name']}`.`{$procedureForeignKey['column_name']}` to `{$procedureForeignKey['referenced_table_name']}`.`{$procedureForeignKey['referenced_column_name']}`." . PHP_EOL;

                $success = false;
            }
        }

        if($success) {
            echo json_decode('"\u2714"') . ' All foreign keys, triggers and procedures are consistent.' . PHP_EOL;
        } else {
            echo json_decode('"\u274c"') . ' Please add DatabaseHelper::updateSoftDeleteTriggersAndProcedures() to your migration(s) or update any inconsistent foreign keys/triggers/procedures (you may need to roll back and re-run your last migration).' . PHP_EOL;
        }

        return $success ? 0 : -1;
    }
}
