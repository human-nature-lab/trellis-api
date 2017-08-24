<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFollowUpQuestionIdToFormSectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_section', function (Blueprint $table) {
            $table->string('follow_up_question_id', 41)->nullable()->after('repeat_prompt_translation_id');
            $table->foreign('follow_up_question_id', 'fk__follow_up_question__question')->references('id')->on('question')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('form_section', function (Blueprint $table) {
            $table->dropForeign('fk__follow_up_question__question');
            $table->dropColumn('follow_up_question_id');
        });
    }
}
