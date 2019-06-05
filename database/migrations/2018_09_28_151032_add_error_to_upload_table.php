<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddErrorToUploadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('upload', function (Blueprint $table) {
            $table->string('error_message')->nullable();
            $table->string('error_code', 64)->nullable();
            $table->text('error_trace')->nullable();
            $table->string('error_line', 64)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('upload', function (Blueprint $table) {
            $table->dropColumn('error_message');
            $table->dropColumn('error_code');
            $table->dropColumn('error_trace');
            $table->dropColumn('error_line');
        });
    }
}
