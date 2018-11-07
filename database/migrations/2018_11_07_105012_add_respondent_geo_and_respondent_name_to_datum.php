<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRespondentGeoAndRespondentNameToDatum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('datum', function (Blueprint $table) {
            $table->string('respondent_geo_id')->nullable();
            $table->string('respondent_name_id')->nullable();

            $table->foreign('respondent_geo_id', 'fk__datum_respondent_geo__idx')->references('id')->on('respondent_geo');
            $table->foreign('respondent_name_id', 'fk__datum_respondent_name__idx')->references('id')->on('respondent_name');
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
            $table->dropForeign('fk__datum_respondent_geo__idx');
            $table->dropForeign('fk__datum_respondent_name__idx');
            $table->dropColumn(['respondent_geo_id', 'respondent_name_id']);
        });
    }
}
