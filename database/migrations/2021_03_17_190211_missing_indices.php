<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MissingIndices extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::table('respondent_name', function (Blueprint $table) {
      $table->index(['name'], 'rn_name_idx');
    });
    Schema::table('condition_tag', function (Blueprint $table) {
      $table->index(['name'], 'ct_name_idx');
    });
    Schema::table('respondent_geo', function (Blueprint $table) {
      $table->index(['is_current'], 'res_geo_is_current_idx');
      $table->index(['respondent_id', 'is_current'], 'res_geo_res_id_is_current_idx');
    });
    Schema::table('question_datum', function (Blueprint $table) {
      $table->index(['created_at'], 'question_datum_created_at_idx');
    });
    Schema::table('survey', function (Blueprint $table) {
      $table->index(['created_at'], 'survey_created_at_idx');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::table('respondent_name', function (Blueprint $table) {
      $table->dropIndex('rn_name_idx');
    });
    Schema::table('condition_tag', function (Blueprint $table) {
      $table->dropIndex('ct_name_idx');
    });
    Schema::table('respondent_geo', function (Blueprint $table) {
      $table->dropIndex('res_geo_is_current_idx');
      $table->dropIndex('res_geo_res_id_is_current_idx');
    });
    Schema::table('question_datum', function (Blueprint $table) {
      $table->dropIndex('question_datum_created_at_idx');
    });
    Schema::table('survey', function (Blueprint $table) {
      $table->dropIndex('survey_created_at_idx');
    });
  }
}
