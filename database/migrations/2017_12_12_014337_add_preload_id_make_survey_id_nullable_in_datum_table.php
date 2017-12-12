<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPreloadIdMakeSurveyIdNullableInDatumTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('datum', function (Blueprint $table) {
            $table->string('preload_id', 41)->nullable()->after('survey_id');
            $table->foreign('preload_id')
                ->references('id')->on('preload')
                ->onUpdate('no action')
                ->onDelete('cascade');

            $table->string('survey_id', 41)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        Schema::table('datum', function (Blueprint $table) {
            $table->string('survey_id', 41)->nullable(false)->change();
            $table->dropForeign('datum_preload_id_foreign');
            $table->dropColumn('preload_id');
        });

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
