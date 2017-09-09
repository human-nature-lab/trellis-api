<?php

use App\Library\DatabaseHelper;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameSoftDeleteCascadeTriggers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // remove previously-named soft delete cascade triggers
        foreach(DatabaseHelper::triggers() as $trigger) {
            if(ends_with($trigger['Trigger'], '_cascade')) {
                DB::unprepared('drop trigger if exists ' . DatabaseHelper::escape($trigger['Trigger']));
            }
        }

        // re-run original soft delete cascade migration to re-create triggers with new names
        (new AddSoftDeleteCascadeToTables)->up();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // since triggers have been renamed by this point, AddSoftDeleteCascadeToTables::down() can roll back using new names
    }
}
