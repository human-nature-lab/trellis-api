<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeConditionIdToConditionTagIdInRespondentConditionTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('respondent_condition_tag', function (Blueprint $table) {
            $table->dropForeign('fk__respondent_condition_tag__condition');
            $table->renameColumn('condition_id', 'condition_tag_id');
            $table->foreign('condition_tag_id', 'fk__respondent_condition_tag__condition_tag')->references('id')->on('condition_tag')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('respondent_condition_tag', function (Blueprint $table) {
            $table->dropForeign('fk__respondent_condition_tag__condition_tag');
            $table->renameColumn('condition_tag_id', 'condition_id');
            $table->foreign('condition_id', 'fk__respondent_condition_tag__condition')->references('id')->on('condition_tag')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }
}
