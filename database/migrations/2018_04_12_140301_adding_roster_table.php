<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddingRosterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roster', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->text('val');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->dateTime('deleted_at')->nullable();
        });
        Schema::table('datum', function (Blueprint $table) {
            $table->string('roster_id', 41)->nullable();
            $table->foreign('roster_id')->references('id')->on('roster');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('datum', function (Blueprint $table) {
            $table->dropForeign('datum_roster_id_foreign');
            $table->dropColumn('roster_id');
        });
        Schema::drop('roster');
    }
}
