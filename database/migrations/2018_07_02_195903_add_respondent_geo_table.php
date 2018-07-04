<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRespondentGeoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up () {
        Schema::create('respondent_geo', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('geo_id', 41);
            $table->string('respondent_id', 41);
            $table->string('previous_respondent_geo_id', 41)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_current')->default(false);
            $table->dateTime('deleted_at')->nullable();
            $table->dateTime('updated_at');
            $table->dateTime('created_at');

            $table->foreign('geo_id')->references('id')->on('geo');
            $table->foreign('previous_respondent_geo_id')->references('id')->on('respondent_geo');
            $table->foreign('respondent_id')->references('id')->on('respondent');
        });
        DB::statement('insert into respondent_geo (id, respondent_id, geo_id, is_current, created_at, updated_at) select UUID(), respondent.id, respondent.geo_id, 1, now(), now() from respondent');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () {
        Schema::dropIfExists('respondent_geo');
    }
}
