<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeStudyFormForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('study_form', function (Blueprint $table) {
            $table->dropForeign('fk__study_form__form');
            $table->foreign('form_master_id', 'fk__study_form__form')
                ->references('id')->on('form')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('study_form', function (Blueprint $table) {
            $table->dropForeign('fk__study_form__form');
            $table->foreign('form_master_id', 'fk__study_form__form')
                ->references('form_master_id')->on('form')
                ->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }
}
