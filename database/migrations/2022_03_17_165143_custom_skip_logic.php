<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CustomSkipLogic extends Migration {
  public function up() {
    Schema::table('skip', function (Blueprint $table) {
      $table->string('custom_logic')->nullable();
    });
  }

  public function down() {
    Schema::table('skip', function (Blueprint $table) {
      $table->dropColumn('custom_logic');
    });
  }
}
