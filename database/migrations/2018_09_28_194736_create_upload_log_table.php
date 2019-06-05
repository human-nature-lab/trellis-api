<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUploadLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('upload_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('upload_id', 41);
            $table->string('table_name');
            $table->string('operation');
            $table->string('row_id', 41);
            $table->text('previous_row')->nullable();
            $table->text('updated_row')->nullable();
            $table->timestamps();

            $table->foreign('upload_id')->references('id')->on('upload');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('upload_log');
    }
}
