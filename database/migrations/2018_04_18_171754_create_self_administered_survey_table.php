<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSelfAdministeredSurveyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('self_administered_survey', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('survey_id', 41);
            $table->enum('login_type', ['url', 'password_only', 'id_password', 'hash'])->default('id_password');
            $table->string('url', 255);
            $table->string('password', 255);
            $table->string('hash', 255);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->dateTime('deleted_at')->nullable();

            $table->foreign('survey_id')->references('id')->on('survey');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('self_administered_survey');
    }
}
