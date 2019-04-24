<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddServerConfigTable extends Migration {

    public function up () {
        Schema::create('config', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('value')->nullable();
            $table->string('type')->default('string');
            $table->boolean('is_public')->default(false);
            $table->string('default_value')->nullable();
            $table->timestamps();
        });
    }

    public function down () {
        Schema::dropIfExists('config');
    }
}
