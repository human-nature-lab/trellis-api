<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePreloadActionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preload_action', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('action_type');
            $table->text('payload')->nullable();
            $table->string('respondent_id', 41);
            $table->string('question_id', 41);
            $table->dateTime('created_at');
            $table->dateTime('deleted_at')->nullable();

            $table->foreign('respondent_id')->references('id')->on('respondent');
            $table->foreign('question_id')->references('id')->on('question');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('preload_action');
    }
}
