<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPreloadActionIdToActionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('action', function (Blueprint $table) {
            $table->string('preload_action_id', 41)->nullable();
            $table->foreign('preload_action_id', 'fk__preload_preload_action__idx')->references('id')->on('preload_action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('action', function (Blueprint $table) {
            $table->dropForeign('fk__preload_preload_action__idx');
            $table->dropColumn('preload_action_id');
        });
    }
}
