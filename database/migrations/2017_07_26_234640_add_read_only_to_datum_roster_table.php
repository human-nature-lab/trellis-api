<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReadOnlyToDatumRosterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('datum_roster', function (Blueprint $table) {
            $table->integer('read_only')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('datum_roster', function (Blueprint $table) {
            $table->dropColumn('read_only');
        });
    }
}
