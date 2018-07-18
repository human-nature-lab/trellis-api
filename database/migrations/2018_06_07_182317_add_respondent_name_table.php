<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Ramsey\Uuid\Uuid;
class AddRespondentNameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('respondent_name', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->boolean('is_display_name')->default(false);
            $table->string('name');
            $table->string('respondent_id', 41);
            $table->string('locale_id', 41)->nullable();
            $table->string('previous_respondent_name_id', 41)->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->dateTime('deleted_at')->nullable();
            $table->foreign('respondent_id')->references('id')->on('respondent');
            $table->foreign('locale_id')->references('id')->on('locale');
        });
        Schema::table('respondent_name', function (Blueprint $table) {
            $table->foreign('previous_respondent_name_id')->references('id')->on('respondent_name');
        });
        // Copy all existing names over as the primary name
        DB::statement('insert into respondent_name (id, respondent_id, name, is_display_name, created_at, updated_at) select UUID(), respondent.id, respondent.name, 1, now(), now() from respondent');
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('respondent_name');
    }
}