<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserConfirmation extends Migration {

  public function up () {
    Schema::create('user_confirmation', function (Blueprint $table) {
      $table->string('key')->primary();
      $table->string('email');
      $table->string('username')->nullable();
      $table->string('password')->nullable();
      $table->boolean('is_confirmed');
      $table->dateTime('created_at');
      $table->dateTime('updated_at');
      $table->dateTime('deleted_at')->nullable();
    });

    Schema::table('user', function (Blueprint $table) {
      $table->string('email')->nullable();
    });
  }

  public function down () {
    Schema::dropIfExists('user_confirmation');
    Schema::table('user', function (Blueprint $table) {
      $table->dropColumn('email');
    });
  }

}
