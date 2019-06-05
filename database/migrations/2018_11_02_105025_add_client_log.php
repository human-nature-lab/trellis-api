<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClientLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_log', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->text('message');
            $table->text('full_message');
            $table->string('severity');
            $table->string('component')->nullable();
            $table->string('sync_id')->nullable();
            $table->string('interview_id')->nullable();
            $table->string('device_id')->nullable();
            $table->string('user_id')->nullable();
            $table->string('version')->nullable();
            $table->boolean('offline')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_log');
    }
}
