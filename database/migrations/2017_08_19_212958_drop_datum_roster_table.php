<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropDatumRosterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('datum_roster');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('datum_roster', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('datum_id', 41)->nullable()->index('fk__datum_roster__datum_idx');
            $table->string('name');
            $table->integer('read_only')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });
    }
}
