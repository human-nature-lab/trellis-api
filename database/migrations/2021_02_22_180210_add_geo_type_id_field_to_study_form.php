<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGeoTypeIdFieldToStudyForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('study_form', function (Blueprint $table) {
            $table->string('geo_type_id', 41)->nullable();
            $table->foreign('geo_type_id', 'fk__geo_type_id_geo_type__idx')->references('id')->on('geo_type');
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
            $table->dropForeign('fk__geo_type_id_geo_type__idx');
            $table->dropColumn(['geo_type_id']);
        });
    }
}
