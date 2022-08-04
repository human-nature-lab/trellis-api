<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PreloadActionPayloadToJson extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::table('preload_action', function (Blueprint $table) {
      $table->json('payload')->nullable()->change();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::table('preload_action', function (Blueprint $table) {
      $table->text('payload')->nullable()->change();
    });
  }
}
