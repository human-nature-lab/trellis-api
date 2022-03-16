<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TokenIndex extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::table('token', function (Blueprint $table) {
      $table->index(['token_hash', 'updated_at'], 'idx__hash_updated_at');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::table('token', function (Blueprint $table) {
      $table->dropIndex('idx__hash_updated_at');
    });
  }
}
