<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSyncTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sync', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('device_id', 41);
            $table->string('snapshot_id', 41)->nullable()->comment('download only');
            $table->string('file_name', 255)->nullable()->comment('upload only');
            $table->string('type', 255)->comment('upload / download');
            $table->string('status', 255)->default('pending')->comment('pending / in_progress / completed / error');
            $table->text('error_message')->nullable()->comment('reason why sync failed');
            $table->text('warning_message')->nullable()->comment('sync was successful but there was a warning, e.g. low disk space');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sync');
    }
}
