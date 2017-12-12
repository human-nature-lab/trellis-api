<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRespondentFillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('respondent_fill', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('respondent_id', 41);
            $table->foreign('respondent_id')
                ->references('id')->on('respondent')
                ->onUpdate('no action')
                ->onDelete('cascade');  // when referenced row is deleted, cascade delete dependent rows
            $table->string('name');
            $table->text('val', 65535);
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
        Schema::drop('respondent_fill');
    }
}
