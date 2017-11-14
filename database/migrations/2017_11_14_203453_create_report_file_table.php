<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_file', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('file_type');
            $table->foreign('report_id', 'fk__report_id')->references('id')->on('report')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->string('file_name');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('report_file');
    }
}
