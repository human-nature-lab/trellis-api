<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActionTable extends Migration
{
    public function up()
    {
        Schema::create('action_type', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('name', 20);
        });
        Schema::create('action', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('survey_id', 41);
            $table->string('action_type_id', 41);
            $table->string('question_id', 41);
            $table->text("action_text");
            $table->dateTime('created_at');
            $table->dateTime('deleted_at')->nullable();

            $table->foreign('question_id')->references('id')->on('question');
            $table->foreign('survey_id')->references('id')->on('survey');
            $table->foreign('action_type_id')->references('id')->on('activity_type');
        });
    }

    public function down()
    {
        Schema::drop('action');
        Schema::drop('action_type');
    }
}
