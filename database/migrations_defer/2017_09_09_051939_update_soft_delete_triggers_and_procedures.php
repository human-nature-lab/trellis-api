<?php

use App\Library\DatabaseHelper;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSoftDeleteTriggersAndProcedures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DatabaseHelper::updateSoftDeleteTriggersAndProcedures();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // since soft delete triggers and procedures are updated declaratively now based on foreign keys, rolling them back is not required
    }
}
