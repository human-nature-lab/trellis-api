<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddZoomLevelToGeoType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('geo_type', function (Blueprint $table) {
            // Add a zoom level column for geo types for storing the default zoom level when displaying on a map
            $table->decimal('zoom_level', 6, 3)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('geo_type', function (Blueprint $table) {
            // Remove geo_type.zoom_level
            $table->dropColumn('zoom_level');
        });
    }
}
