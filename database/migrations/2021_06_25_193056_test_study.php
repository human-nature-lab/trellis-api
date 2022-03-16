<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TestStudy extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::table('study', function (Blueprint $table) {
      // $table->boolean('test')->default(false);
      $table->string('test_study_id')->nullable();
      $table->foreign('test_study_id', 'fk__study_test_study__idx')->references('id')->on('study');
    });

    Schema::table('study_form', function (Blueprint $table) {
      $table->string('current_version_id')->nullable();
      $table->foreign('current_version_id', 'fk__study_form_current_version__idx')->references('id')->on('form');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::table('study', function (Blueprint $table) {
      // $table->dropColumn('test');
      $table->dropForeign('fk__study_test_study__idx');
      $table->dropColumn('test_study_id');
    });

    Schema::table('study_form', function (Blueprint $table) {
      $table->dropForeign('fk__study_form_current_version__idx');
      $table->dropColumn('current_version_id');
    });
  }
}
