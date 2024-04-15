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
      $table->boolean('should_sync')->default(false)->comment('Whether the asset should be downloaded to the client');
      $table->timestamps();
      $table->softDeletes();
    });

    Schema::create('study_asset', function (Blueprint $table) {
      $table->uuid('asset_id')->comment('The asset id')->references('id')->on('asset');
      $table->uuid('study_id')->comment('The study id')->references('id')->on('study');

      $table->primary(['asset_id', 'study_id']);
    });
  }


  public function down() {
    Schema::dropIfExists('asset');
  }
}
