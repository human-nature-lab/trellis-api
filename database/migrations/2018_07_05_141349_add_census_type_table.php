<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCensusTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('census_type', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('name');
        });
        Schema::table('study_form', function (Blueprint $table) {
            $table->string('census_type_id')->nullable();
            $table->foreign('census_type_id', 'fk__study_form_census_type__idx')->references('id')->on('census_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('study_form', function (Blueprint $table) {
            $table->dropForeign('fk__study_form_census_type__idx');
            $table->dropColumn('census_type_id');
        });
        Schema::dropIfExists('census_type');
    }
}
