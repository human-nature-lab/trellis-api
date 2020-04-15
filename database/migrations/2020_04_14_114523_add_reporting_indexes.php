<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReportingIndexes extends Migration {

    public function up () {
        Schema::table('question_datum', function (Blueprint $table) {
            $table->index(['survey_id', 'created_at'], 'idx_survey_id_created_at');
        });
        Schema::table('translation_text', function (Blueprint $table) {
            $table->index(['translation_id', 'locale_id'], 'idx_translation_locale');
        });
        Schema::table('respondent_geo', function (Blueprint $table) {
            $table->index(['respondent_id', 'is_current'], 'idx_current_respondent_id');
            $table->index('is_current', 'idx_is_current');
        });
    }

    public function down () {
        Schema::table('question_datum', function (Blueprint $table) {
            $table->dropIndex('idx_survey_id_created_at');
        });
        Schema::table('translation_text', function (Blueprint $table) {
            $table->dropIndex('idx_translation_locale');
        });
        Schema::table('respondent_geo', function (Blueprint $table) {
            $table->dropIndex('idx_current_respondent_id');
            $table->dropIndex('idx_is_current');
        });
    }

}
