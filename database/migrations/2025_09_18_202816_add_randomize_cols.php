<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRandomizeCols extends Migration {

  public function up() {
    Schema::table('form_section', function (Blueprint $table) {
      $table->boolean('randomize_pages')->default(false);
    });
    Schema::table('section_question_group', function (Blueprint $table) {
      $table->boolean('randomize_questions')->default(false);
    });
  }


  public function down() {
    Schema::table('form_section', function (Blueprint $table) {
      $table->dropColumn('randomize_pages');
    });
    Schema::table('section_question_group', function (Blueprint $table) {
      $table->dropColumn('randomize_questions');
    });
  }
}
