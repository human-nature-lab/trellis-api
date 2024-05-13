<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAsset extends Migration {

  public function up() {
    Schema::create('asset', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->string('file_name')->comment('The original name of the asset');
      $table->string('type')->comment('The general type of the asset');
      $table->integer('size')->comment('The size of the asset in bytes');
      $table->string('mime_type')->comment('The specific MIME type of the asset');
      $table->string('md5_hash')->nullable()->comment('The MD5 hash of the asset');
      $table->boolean('is_from_survey')->default(false)->comment('Whether the asset was uploaded as part of a survey');
      $table->timestamps();
      $table->softDeletes();
    });
    
    Schema::table('datum', function (Blueprint $table) {
      $table->uuid('asset_id')->nullable()->comment('The asset id')->references('id')->on('asset');
    });
  }


  public function down() {
    Schema::table('datum', function (Blueprint $table) {
      $table->dropColumn('asset_id');
    });
    Schema::dropIfExists('asset');
  }
}
