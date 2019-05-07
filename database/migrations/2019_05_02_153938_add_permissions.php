<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPermissions extends Migration {

  public function up () {
    Schema::create('role', function (Blueprint $table) {
      $table->string('id')->primary();
      $table->string('name');
      $table->boolean('can_delete')->default(true);
      $table->boolean('can_edit')->default(true);
      $table->dateTime('created_at');
      $table->dateTime('updated_at');
      $table->dateTime('deleted_at')->nullable();
    });
    Schema::create('permission', function (Blueprint $table) {
      $table->string('id')->primary();
      $table->string('type');
    });
    Schema::create('role_permission', function (Blueprint $table) {
      $table->increments('id');
      $table->string('role_id');
      $table->string('permission_id');
      $table->boolean('value');
      $table->dateTime('created_at');
      $table->dateTime('updated_at');
      $table->dateTime('deleted_at')->nullable();

      $table->foreign('role_id')->references('id')->on('role');
      $table->foreign('permission_id')->references('id')->on('permission');
    });

    Schema::table('user', function (Blueprint $table) {
      $table->string('role_id')->nullable();
      $table->foreign('role_id', 'fk__user_role__idx')->references('id')->on('role');
    });
  }

  public function down () {
    Schema::table('user', function (Blueprint $table) {
      $table->dropForeign('fk__user_role__idx');
      $table->dropColumn('role_id');
    });
    Schema::dropIfExists('role_permission');
    Schema::dropIfExists('role');
    Schema::dropIfExists('permission');
  }
}
