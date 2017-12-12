<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudyParameterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('study_parameter', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('study_id', 41);
            $table->foreign('study_id')
                ->references('id')->on('study')
                ->onUpdate('no action')
                ->onDelete('cascade');  // when referenced row is deleted, cascade delete dependent rows
            $table->string('parameter_id', 41);
            $table->foreign('parameter_id')
                ->references('id')->on('parameter')
                ->onUpdate('no action')
                ->onDelete('cascade');  // when referenced row is deleted, cascade delete dependent rows
            $table->string('val');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('study_parameter');
    }
}
