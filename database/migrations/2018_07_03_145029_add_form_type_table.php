<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFormTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up () {
        Schema::create('form_type', function (Blueprint $table) {
            $table->tinyInteger('id')->primary();
            $table->string('name');
        });
        Schema::table('study_form', function (Blueprint $table) {
            $table->dropColumn('form_type');
            $table->tinyInteger('form_type_id');
            $table->foreign('form_type_id', 'fk__form_study_form_type_idx')->references('id')->on('form_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () {
        Schema::table('study_form', function (Blueprint $table) {
            $table->dropForeign('fk__form_study_form_type_idx');
            $table->dropColumn('form_type_id');
            $table->tinyInteger('form_type');
        });
        Schema::dropIfExists('form_type');
    }
}
