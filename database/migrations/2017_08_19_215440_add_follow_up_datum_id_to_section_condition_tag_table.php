<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFollowUpDatumIdToSectionConditionTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('section_condition_tag', function (Blueprint $table) {
            $table->string('follow_up_datum_id', 41)->nullable()->after('repetition');
            // Set follow_up_datum_id to null if the referenced datum is deleted
            $table->foreign('follow_up_datum_id', 'fk__follow_up_datum__datum')->references('id')->on('datum')->onUpdate('NO ACTION')->onDelete('SET NULL');
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
            $table->dropForeign('fk__follow_up_datum__datum');
            $table->dropColumn('follow_up_datum_id');
        });
    }
}
