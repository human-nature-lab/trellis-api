<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GeoAssignedId extends Migration {

  public function up () {
    Schema::table('geo', function (Blueprint $table) {
      $table->string('assigned_id')->nullable();
    });
  }


  public function down () {
    Schema::table('geo', function (Blueprint $table) {
      $table->dropColumn('assigned_id');
    });
  }
}
