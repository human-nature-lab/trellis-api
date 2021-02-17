<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateReportFile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
      Schema::table('report_file', function (Blueprint $table) {
        $table->integer('size')->nullable();
        $table->string('data_type')->nullable(false);
        $table->string('file_type')->nullable(false)->change();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()  {
      Schema::table('report_file', function (Blueprint $table) {
        $table->dropColumn('size');
        $table->dropColumn('data_type');
        $table->string('file_type')->nullable(true)->change();
      });
    }
}
