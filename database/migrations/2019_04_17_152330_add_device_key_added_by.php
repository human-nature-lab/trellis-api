<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeviceKeyAddedBy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up () {
        Schema::table('device', function (Blueprint $table) {
            $table->string('key')->nullable();
            $table->string('added_by_user_id')->nullable();
            $table->foreign('added_by_user_id', 'fk__device_added_by__idx')->references('id')->on('user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('device', function (Blueprint $table) {
            $table->dropForeign('fk__device_added_by__idx');
            $table->dropColumn(['key', 'added_by_user_id']);
        });
    }
}
