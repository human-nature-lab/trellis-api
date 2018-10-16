<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeRespondentGeoGeoIdNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('respondent_geo', function (Blueprint $table) {
            $table->string('geo_id', 41)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('respondent_geo', function (Blueprint $table) {
            $table->string('geo_id', 41)->change();
        });
    }
}
