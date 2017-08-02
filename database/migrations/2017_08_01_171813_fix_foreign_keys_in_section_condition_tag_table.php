<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixForeignKeysInSectionConditionTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('section_condition_tag', function (Blueprint $table) {
            $table->dropForeign('fk__section_condition_tag__section');
            $table->dropForeign('fk__section_condition_tag__condition_tag');
            $table->dropForeign('fk__section_condition_tag__survey');

            $table->foreign('section_id', 'fk__section_condition_tag__section')->references('id')->on('section')->onUpdate('no action')->onDelete('no action');
            $table->foreign('condition_id', 'fk__section_condition_tag__condition_tag')->references('id')->on('condition_tag')->onUpdate('no action')->onDelete('no action');
            $table->foreign('survey_id', 'fk__section_condition_tag__survey')->references('id')->on('survey')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('section_condition_tag', function (Blueprint $table) {
            $table->dropForeign('fk__section_condition_tag__section');
            $table->dropForeign('fk__section_condition_tag__condition_tag');
            $table->dropForeign('fk__section_condition_tag__survey');

            $table->foreign('condition_id', 'fk__section_condition_tag__condition_tag')->references('id')->on('condition_tag')->onUpdate('no action')->onDelete('no action');

            //NOTE this was not originally desired but is required for rollback
            $table->foreign('id', 'fk__section_condition_tag__section')->references('id')->on('section')->onUpdate('no action')->onDelete('no action');
            $table->foreign('id', 'fk__section_condition_tag__survey')->references('id')->on('survey')->onUpdate('no action')->onDelete('no action');
        });
    }
}
