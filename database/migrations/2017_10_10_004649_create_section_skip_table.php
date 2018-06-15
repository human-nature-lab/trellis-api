<?php

// use App\Library\DatabaseHelper;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSectionSkipTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('section_skip', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('section_id', 41);
            $table->foreign('section_id')
                ->references('id')->on('section')
                ->onUpdate('no action')
                ->onDelete('cascade');  // when referenced row is deleted, cascade delete dependent rows
            $table->string('skip_id', 41);
            $table->foreign('skip_id')
                ->references('id')->on('skip')
                ->onUpdate('no action')
                ->onDelete('cascade');  // when referenced row is deleted, cascade delete dependent rows
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        // DatabaseHelper::updateSoftDeleteTriggersAndProcedures();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('section_skip');

        // since soft delete triggers and procedures are updated declaratively now based on foreign keys, rolling them back is not required
    }
}
