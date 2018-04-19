<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AllowNullableQuestionDatumFollowUpDatumId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('question_datum', function (Blueprint $table) {
            $table->string('follow_up_datum_id', 41)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('question_datum', function (Blueprint $table) {
            $table->string('follow_up_datum_id', 41)->nullable(false)->change();
        });
    }
}
