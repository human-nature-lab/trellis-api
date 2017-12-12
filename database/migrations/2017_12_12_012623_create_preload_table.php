<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePreloadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preload', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('respondent_id', 41);
            $table->foreign('respondent_id')
                ->references('id')->on('respondent')
                ->onUpdate('no action')
                ->onDelete('cascade');  // when referenced row is deleted, cascade delete dependent rows
            $table->string('form_id', 41);
            $table->foreign('form_id')
                ->references('id')->on('form')
                ->onUpdate('no action')
                ->onDelete('cascade');  // when referenced row is deleted, cascade delete dependent rows
            $table->string('study_id', 41);
            $table->foreign('study_id')
                ->references('id')->on('study')
                ->onUpdate('no action')
                ->onDelete('cascade');  // when referenced row is deleted, cascade delete dependent rows
            $table->string('last_question_id', 41)->nullable();
            $table->foreign('last_question_id')
                ->references('id')->on('question')
                ->onUpdate('no action')
                ->onDelete('cascade');  // when referenced row is deleted, cascade delete dependent rows
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
            $table->dateTime('completed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('preload');
    }
}
