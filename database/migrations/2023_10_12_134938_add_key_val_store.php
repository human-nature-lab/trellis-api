<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKeyValStore extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('kv', function (Blueprint $table) {
      $table->string('id', 41)->primary();
      $table->string('namespace')->default('default');
      $table->string('key');
      $table->text('value')->nullable();
      $table->dateTime('created_at');
      $table->dateTime('updated_at');
      $table->dateTime('deleted_at')->nullable();
      $table->unique(['namespace', 'key'], 'idxu_kv_namespace_key');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('kv');
  }
}
