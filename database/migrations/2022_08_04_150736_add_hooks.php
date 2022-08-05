<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHooks extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('hook', function (Blueprint $table) {
      $table->integer('id', true);
      $table->string('hook_id')->index();
      $table->string('entity_id')->index();
      $table->string('instance_id')->index()->nullable();
      $table->json('result')->nullable();
      $table->dateTime('started_at');
      $table->dateTime('finished_at')->nullable();
      $table->unique(['hook_id', 'entity_id', 'instance_id']);
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('hook');
  }
}
