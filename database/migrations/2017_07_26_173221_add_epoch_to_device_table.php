<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEpochToDeviceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('device', function (Blueprint $table) {
            $table->bigInteger('epoch')->unsigned()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('device', function ($table) {
        //     $table->dropForeign('user_providers_user_id_foreign');
        // });
        Schema::table('device', function (Blueprint $table) {
            $table->dropColumn('epoch');
        });
    }
}
