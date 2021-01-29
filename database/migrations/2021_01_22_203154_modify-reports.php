<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyReports extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::table('report', function (Blueprint $table) {
      $table->renameColumn('type', 'name');
      $table->string('config')->nullable();
      $table->dropForeign('fk__form_id_form__idx');
      $table->dropColumn('form_id');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::table('report', function (Blueprint $table) {
      $table->renameColumn('name', 'type');
      $table->dropColumn('config');
      $table->string('form_id', 41)->nullable();
      $table->foreign('form_id', 'fk__form_id_form__idx')->references('id')->on('form');
    });
  }
}
