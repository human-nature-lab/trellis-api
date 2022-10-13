<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFormConfig extends Migration {
  public function up() {
    Schema::table('study_form', function (Blueprint $table) {
      $table->boolean('allow_multiple_responses');
      $table->boolean('allow_public_responses');
    });
  }

  public function down() {
    Schema::table('study_form', function (Blueprint $table) {
      $table->dropColumn('allow_public_responses');
      $table->dropColumn('allow_multiple_responses');
    });
  }
}
