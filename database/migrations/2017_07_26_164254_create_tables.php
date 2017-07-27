<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTables extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assign_condition_tag', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('condition_tag_id', 41)->index('fk__assign_condition_tag__condition_idx');
            $table->text('logic', 65535);
            $table->string('scope', 64)->nullable()->comment('RESPONDENT / SURVEY');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('choice', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('choice_translation_id', 41)->index('fk__choice__translation_idx');
            $table->string('val');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('condition_tag', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('name')->comment('0 = show_if, 1 = hide_if, 2-255 reserved');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('datum_choice', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('datum_id', 41)->index('FK__datum_choice__datum_idx');
            $table->string('choice_id', 41)->index('FK__datum_choice__choice_idx');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('datum_geo', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('datum_id', 41)->index('FK__datum_geo__datum_idx');
            $table->string('geo_id', 41)->index('FK__datum_geo__geo_idx');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('datum_group_tag', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('datum_id', 41)->index('FK__datum_group_tag__datum_idx');
            $table->string('group_tag_id', 41)->index('FK__datum_group_tag__group_tag_idx');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('datum_photo', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('datum_id', 41)->index('fk__datum_photo__datum_idx');
            $table->string('photo_id', 41)->index('fk__datum_photo__photo_idx');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->text('notes', 65535)->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('datum_roster', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('datum_id', 41)->nullable()->index('fk__datum_roster__datum_idx');
            $table->string('name');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('datum', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('name');
            $table->text('val', 65535);
            $table->string('choice_id', 41)->nullable()->index('fk__datum__choice_idx');
            $table->string('survey_id', 41)->index('fk__datum__survey_idx');
            $table->string('question_id', 41)->nullable()->index('fk__datum__question_idx');
            $table->integer('repetition')->unsigned()->default(0)->comment('0 = Not repeated
1 - n = # of the repeated section');
            $table->string('parent_datum_id', 41)->nullable()->index('fk__parent_datum_id__datum_idx');
            $table->string('datum_type_id', 41)->default('0')->index('fk__datum__datum_type_idx');
            $table->integer('sort_order')->unsigned()->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('datum_type', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('name');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('device', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('device_id', 64)->nullable();
            $table->string('name');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('edge_datum', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('edge_id', 41)->index('fk__edge_datum__edge_idx');
            $table->string('datum_id', 41)->index('fk__edge_datum__datum_idx');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('edge', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('source_respondent_id', 41)->index('fk__edge_source_respondent_id__respondent_idx');
            $table->string('target_respondent_id', 41)->index('fk__edge_target_respondent_id__respondent_idx');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('form_section', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('form_id', 41)->nullable()->index('fk__form_section__form_idx');
            $table->string('section_id', 41)->index('fk__form_section__section_idx');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->boolean('is_repeatable')->default(0);
            $table->unsignedTinyInteger('max_repetitions')->default(0)->comment('Max number of repetitions allowed, 0 = no limit');
            $table->string('repeat_prompt_translation_id', 41)->nullable()->index('fk__form_section_repeat_prompt__translation_idx');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('form_skip', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('form_id', 41)->index('fk__form_skip__form_idx');
            $table->string('skip_id', 41)->index('fk__form_skip__skip_idx');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('form', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('form_master_id', 41)->index('idx__form_master_id');
            $table->string('name_translation_id', 41)->index('fk__form_name__translation_idx');
            $table->integer('version')->unsigned()->default(0);
            $table->boolean('is_published')->default(0);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('geo_photo', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('geo_id', 41)->index('fk__geo_photo__geo_idx');
            $table->string('photo_id', 41)->index('fk__geo_photo__photo_idx');
            $table->unsignedTinyInteger('sort_order');
            $table->text('notes', 65535)->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('geo', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('geo_type_id', 41)->index('fk__geo__geo_type_idx');
            $table->string('parent_id', 41)->nullable()->index('fk__geo__parent_geo_idx');
            $table->float('latitude', 10, 0)->nullable();
            $table->float('longitude', 10, 0)->nullable();
            $table->float('altitude', 10, 0)->nullable();
            $table->string('name_translation_id', 41)->index('fk__name_translation__translation_idx');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('geo_type', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('parent_id', 41)->nullable();
            $table->string('study_id', 41)->index('FK__geo_type__study_idx');
            $table->string('name');
            $table->boolean('can_enumerator_add')->default(0);
            $table->boolean('can_contain_respondent')->default(0);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('group_tag', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('group_tag_type_id', 41)->index('fk__group__group_type_idx');
            $table->string('name');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('group_tag_type', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('name');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('interview_question', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('interview_id', 41)->index('fk__interview_question__survey_session_idx');
            $table->string('question_id', 41)->index('fk__interview_question__question_idx');
            $table->dateTime('enter_date')->nullable();
            $table->dateTime('answer_date')->nullable();
            $table->dateTime('leave_date')->nullable();
            $table->integer('elapsed_time')->unsigned()->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('interview', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('survey_id', 41)->index('fk__survey_session__survey_idx');
            $table->string('user_id', 41)->index('fk__survey_session__user_idx');
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->string('latitude', 45)->nullable();
            $table->string('longitude', 45)->nullable();
            $table->string('altitude', 45)->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('key', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('name', 128)->default('')->unique('key_name_UNIQUE');
            $table->string('hash', 32)->default('')->unique('key_hash_UNIQUE');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('locale', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('language_tag')->nullable();
            $table->string('language_name', 64)->nullable()->comment('The English name of the language.');
            $table->string('language_native', 64)->nullable()->comment('The name of the language in the language itself.');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('log', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('actor_id', 41);
            $table->string('row_id', 41);
            $table->string('table_name', 64)->nullable();
            $table->string('operation', 64)->nullable();
            $table->text('previous_row', 65535)->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('parameter', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('name');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('photo', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('file_name');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('photo_tag', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('photo_id', 41)->index('fk__photo_tag__photo_idx');
            $table->string('tag_id', 41)->index('fk__photo_tag__tag_idx');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('question_assign_condition_tag', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('question_id', 41)->index('fk__question_assign_condition_tag__question_idx');
            $table->string('assign_condition_tag_id', 41)->index('fk__question_assign_condition_tag__assign_condition_tag_idx');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('question_choice', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('question_id', 41)->index('fk__question_choice__question_idx');
            $table->string('choice_id', 41)->index('fk__question_choice__choice_idx');
            $table->integer('sort_order')->unsigned()->default(0);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('question_group_skip', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('question_group_id', 41)->index('fk__question_group_skip__question_group_idx');
            $table->string('skip_id', 41)->index('fk__form_skip__skip_idx');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('question_group', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('question_parameter', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('question_id', 41)->index('fk__question_parameter__question_idx');
            $table->string('parameter_id', 41)->index('fk__question_parameter__parameter_idx');
            $table->string('val');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('question', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('question_type_id', 41)->index('fk__question__question_type_idx');
            $table->string('question_translation_id', 41)->index('fk__question__translation_idx');
            $table->string('question_group_id', 41)->index('fk__question__question_group_idx');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->string('var_name');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('question_type', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('name');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('respondent_condition_tag', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('respondent_id', 41)->index('fk__respondent_condition__respondent_idx');
            $table->string('condition_id', 41)->index('fk__respondent_condition__condition_idx');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('respondent_group_tag', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('respondent_id', 41)->index('fk__respondent_group__respondent_idx');
            $table->string('group_tag_id', 41)->index('fk__respondent_group__group_idx');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('respondent_photo', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('respondent_id', 41)->index('fk__respondent_photo__respondent_idx');
            $table->string('photo_id', 41)->index('fk__respondent_photo__photo_idx');
            $table->unsignedTinyInteger('sort_order');
            $table->text('notes', 65535)->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('respondent', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('geo_id', 41)->nullable()->index('fk__respondent__geo_idx');
            $table->text('notes', 65535)->nullable();
            $table->text('geo_notes', 65535)->nullable();
            $table->string('name');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('section_condition_tag', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('section_id', 41);
            $table->string('condition_id', 41)->index('fk__section_condition__condition_tag_idx');
            $table->string('survey_id', 41);
            $table->integer('repetition')->unsigned()->default(0);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('section_question_group', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('section_id', 41)->index('fk__form_question__form_idx');
            $table->string('question_group_id', 41)->index('fk__section_question_group__question_group_idx');
            $table->integer('question_group_order')->unsigned()->default(0);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('section', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('name_translation_id', 41)->index('fk__section_name__translation_idx');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('skip_condition_tag', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('skip_id', 41)->index('fk__skip_condition_tag__skip_idx')->comment('0 = show_if, 1 = hide_if, 2-255 reserved');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
            $table->string('condition_tag_name');
        });

        Schema::create('skip', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->unsignedTinyInteger('show_hide')->comment('0 = show_if, 1 = hide_if, 2-255 reserved');
            $table->unsignedTinyInteger('any_all')->comment('0 = Any, 1 = All');
            $table->unsignedTinyInteger('precedence')->default(0);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('study_form', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('study_id', 41)->index('fk__study_form__study_idx');
            $table->string('form_master_id', 41)->index('fk__study_form__form_idx');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
            $table->unsignedTinyInteger('form_type')->default(0)->comment('0 = default, 1 = census, 2 = surveyor, 3 - 255 reserved');
        });

        Schema::create('study_locale', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('study_id', 41)->index('fk__study_locale__study_idx');
            $table->string('locale_id', 41)->index('fk__study_locale__locale_idx');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('study_respondent', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('study_id', 41)->index('fk__study_respondent__study_idx');
            $table->string('respondent_id', 41)->index('fk__study_respondent__respondent_idx');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('study', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('name');
            $table->unsignedTinyInteger('photo_quality')->default(60);
            $table->string('default_locale_id', 41)->index('fk__study__default_locale_idx');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('survey_condition_tag', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('survey_id', 41)->index('fk__survey_condition_tag__survey_idx');
            $table->string('condition_id', 41)->index('fk__interview_condition__condition_idx');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('survey', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('respondent_id', 41)->index('fk__survey__respondent_idx');
            $table->string('form_id', 41)->index('fk__survey__form_idx');
            $table->string('study_id', 41)->index('fk__survey__study_idx');
            $table->string('last_question_id', 41)->nullable()->index('fk__survey__last_question_idx');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
            $table->dateTime('completed_at')->nullable();
        });

        Schema::create('tag', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('name', 63);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('token', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('user_id', 41);
            $table->string('token_hash', 128)->unique('token_hash_UNIQUE');
            $table->bigInteger('key_id')->unsigned();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('translation', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('translation_text', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('translation_id', 41)->index('fk__translation_text__translation_idx');
            $table->string('locale_id', 41)->nullable()->index('fk__translation_text__locale_idx');
            $table->text('translated_text', 65535);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('user_study', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('user_id', 41)->index('fk__user_study__user_idx');
            $table->string('study_id', 41)->index('fk__user_study__study_idx');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
        });

        Schema::create('user', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('name');
            $table->string('username', 63);
            $table->string('password', 63);
            $table->string('role', 64)->nullable()->comment('ADMIN / SURVEYOR');
            $table->string('selected_study_id', 41)->nullable()->index('fk__user_selected_study__study_idx');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            // $table->softDeletes();
            $table->unique(['username','deleted_at'], 'idx__username__deleted_at');
        });

        Schema::table('assign_condition_tag', function (Blueprint $table) {
            $table->foreign('condition_tag_id', 'fk__assign_condition_tag__condition')->references('id')->on('condition_tag')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('choice', function (Blueprint $table) {
            $table->foreign('choice_translation_id', 'fk__choice__translation')->references('id')->on('translation')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('datum_choice', function (Blueprint $table) {
            $table->foreign('choice_id', 'FK__datum_choice__choice')->references('id')->on('choice')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('datum_id', 'FK__datum_choice__datum')->references('id')->on('datum')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('datum_geo', function (Blueprint $table) {
            $table->foreign('datum_id', 'FK__datum_geo__datum')->references('id')->on('datum')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('geo_id', 'FK__datum_geo__geo')->references('id')->on('geo')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('datum_group_tag', function (Blueprint $table) {
            $table->foreign('datum_id', 'FK__datum_group_tag__datum')->references('id')->on('datum')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('group_tag_id', 'FK__datum_group_tag__group_tag')->references('id')->on('group_tag')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('datum_photo', function (Blueprint $table) {
            $table->foreign('datum_id', 'fk__datum_photo__datum')->references('id')->on('datum')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('photo_id', 'fk__datum_photo__photo')->references('id')->on('photo')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('datum', function (Blueprint $table) {
            $table->foreign('choice_id', 'fk__datum__choice')->references('id')->on('choice')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('datum_type_id', 'fk__datum__datum_type')->references('id')->on('datum_type')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('question_id', 'fk__datum__question')->references('id')->on('question')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('survey_id', 'fk__datum__survey')->references('id')->on('survey')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('parent_datum_id', 'fk__parent_datum_id__datum')->references('id')->on('datum')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('edge_datum', function (Blueprint $table) {
            $table->foreign('edge_id', 'fk__connection_datum__connection')->references('id')->on('edge')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('datum_id', 'fk__connection_datum__datum')->references('id')->on('datum')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('edge', function (Blueprint $table) {
            $table->foreign('source_respondent_id', 'fk__edge_list_source__respondent')->references('id')->on('respondent')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('target_respondent_id', 'fk__edge_list_target__respondent')->references('id')->on('respondent')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('form_section', function (Blueprint $table) {
            $table->foreign('form_id', 'fk__form_section__form')->references('id')->on('form')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('section_id', 'fk__form_section__section')->references('id')->on('section')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('repeat_prompt_translation_id', 'fk__form_section_repeat_prompt__translation')->references('id')->on('translation')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('form_skip', function (Blueprint $table) {
            $table->foreign('form_id', 'fk__form_skip__form')->references('id')->on('form')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('skip_id', 'fk__form_skip__skip')->references('id')->on('skip')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('form', function (Blueprint $table) {
            $table->foreign('name_translation_id', 'fk__form_name__translation')->references('id')->on('translation')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('geo_photo', function (Blueprint $table) {
            $table->foreign('geo_id', 'fk__geo_photo__geo')->references('id')->on('geo')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('photo_id', 'fk__geo_photo__photo')->references('id')->on('photo')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('geo', function (Blueprint $table) {
            $table->foreign('geo_type_id', 'fk__geo__geo_type')->references('id')->on('geo_type')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('parent_id', 'fk__geo__parent_geo')->references('id')->on('geo')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('name_translation_id', 'fk__geo_name__translation')->references('id')->on('translation')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('geo_type', function (Blueprint $table) {
            $table->foreign('study_id', 'FK__geo_type__study')->references('id')->on('study')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('group_tag', function (Blueprint $table) {
            $table->foreign('group_tag_type_id', 'fk__group_tag__group_tag_type')->references('id')->on('group_tag_type')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('interview_question', function (Blueprint $table) {
            $table->foreign('interview_id', 'fk__interview_question__interview')->references('id')->on('interview')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('question_id', 'fk__interview_question__question')->references('id')->on('question')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('interview', function (Blueprint $table) {
            $table->foreign('survey_id', 'fk__survey_session__survey')->references('id')->on('survey')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('user_id', 'fk__survey_session__user')->references('id')->on('user')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('photo_tag', function (Blueprint $table) {
            $table->foreign('photo_id', 'fk__photo_tag__photo')->references('id')->on('photo')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('tag_id', 'fk__photo_tag__tag')->references('id')->on('tag')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('question_assign_condition_tag', function (Blueprint $table) {
            $table->foreign('assign_condition_tag_id', 'fk__question_assign_condition_tag__assign_condition_tag')->references('id')->on('assign_condition_tag')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('question_id', 'fk__question_assign_condition_tag__question')->references('id')->on('question')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('question_choice', function (Blueprint $table) {
            $table->foreign('choice_id', 'fk__question_choice__choice')->references('id')->on('choice')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('question_id', 'fk__question_choice__question')->references('id')->on('question')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('question_group_skip', function (Blueprint $table) {
            $table->foreign('question_group_id', 'fk__question_group_skip__question_group')->references('id')->on('question_group')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('skip_id', 'fk__question_group_skip__skip')->references('id')->on('skip')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('question_parameter', function (Blueprint $table) {
            $table->foreign('parameter_id', 'fk__question_parameter__parameter')->references('id')->on('parameter')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('question_id', 'fk__question_parameter__question')->references('id')->on('question')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('question', function (Blueprint $table) {
            $table->foreign('question_group_id', 'fk__question__question_group')->references('id')->on('question_group')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('question_type_id', 'fk__question__question_type')->references('id')->on('question_type')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('question_translation_id', 'fk__question__translation')->references('id')->on('translation')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('respondent_condition_tag', function (Blueprint $table) {
            $table->foreign('condition_id', 'fk__respondent_condition_tag__condition')->references('id')->on('condition_tag')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('respondent_id', 'fk__respondent_condition_tag__respondent')->references('id')->on('respondent')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('respondent_group_tag', function (Blueprint $table) {
            $table->foreign('group_tag_id', 'fk__respondent_group_tag__group_tag')->references('id')->on('group_tag')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('respondent_id', 'fk__respondent_group_tag__respondent')->references('id')->on('respondent')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('respondent_photo', function (Blueprint $table) {
            $table->foreign('photo_id', 'fk__respondent_photo__photo')->references('id')->on('photo')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('respondent_id', 'fk__respondent_photo__respondent')->references('id')->on('respondent')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('respondent', function (Blueprint $table) {
            $table->foreign('geo_id', 'fk__respondent__geo')->references('id')->on('geo')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('section_condition_tag', function (Blueprint $table) {
            $table->foreign('condition_id', 'fk__section_condition_tag__condition_tag')->references('id')->on('condition_tag')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('id', 'fk__section_condition_tag__section')->references('id')->on('section')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('id', 'fk__section_condition_tag__survey')->references('id')->on('survey')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('section_question_group', function (Blueprint $table) {
            $table->foreign('question_group_id', 'fk__section_question_group__question_group')->references('id')->on('question_group')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('section_id', 'fk__section_question_group__section')->references('id')->on('section')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('section', function (Blueprint $table) {
            $table->foreign('name_translation_id', 'fk__section_name__translation')->references('id')->on('translation')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('skip_condition_tag', function (Blueprint $table) {
            $table->foreign('skip_id', 'fk__skip_condition_tag__skip')->references('id')->on('skip')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('study_form', function (Blueprint $table) {
            $table->foreign('form_master_id', 'fk__study_form__form')->references('form_master_id')->on('form')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('study_id', 'fk__study_form__study')->references('id')->on('study')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('study_locale', function (Blueprint $table) {
            $table->foreign('locale_id', 'fk__study_locale__locale')->references('id')->on('locale')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('study_id', 'fk__study_locale__study')->references('id')->on('study')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('study_respondent', function (Blueprint $table) {
            $table->foreign('respondent_id', 'fk__study_respondent__respondent')->references('id')->on('respondent')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('study_id', 'fk__study_respondent__study')->references('id')->on('study')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('study', function (Blueprint $table) {
            $table->foreign('default_locale_id', 'fk__study__default_locale')->references('id')->on('locale')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('survey_condition_tag', function (Blueprint $table) {
            $table->foreign('condition_id', 'fk__survey_condition_tag__condition_tag')->references('id')->on('condition_tag')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('survey_id', 'fk__survey_condition_tag__survey')->references('id')->on('survey')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('survey', function (Blueprint $table) {
            $table->foreign('form_id', 'fk__survey__form')->references('id')->on('form')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('last_question_id', 'fk__survey__last_question')->references('id')->on('question')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('respondent_id', 'fk__survey__respondent')->references('id')->on('respondent')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('study_id', 'fk__survey__study')->references('id')->on('study')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('translation_text', function (Blueprint $table) {
            $table->foreign('locale_id', 'fk__translation_text__locale')->references('id')->on('locale')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('translation_id', 'fk__translation_text__translation')->references('id')->on('translation')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('user_study', function (Blueprint $table) {
            $table->foreign('study_id', 'fk__user_study__study')->references('id')->on('study')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('user_id', 'fk__user_study__user')->references('id')->on('user')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('user', function (Blueprint $table) {
            $table->foreign('selected_study_id', 'fk__user_selected_study__study')->references('id')->on('study')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropForeign('fk__user_selected_study__study');
        });

        Schema::table('user_study', function (Blueprint $table) {
            $table->dropForeign('fk__user_study__study');
            $table->dropForeign('fk__user_study__user');
        });

        Schema::table('translation_text', function (Blueprint $table) {
            $table->dropForeign('fk__translation_text__locale');
            $table->dropForeign('fk__translation_text__translation');
        });

        Schema::table('survey', function (Blueprint $table) {
            $table->dropForeign('fk__survey__form');
            $table->dropForeign('fk__survey__last_question');
            $table->dropForeign('fk__survey__respondent');
            $table->dropForeign('fk__survey__study');
        });

        Schema::table('survey_condition_tag', function (Blueprint $table) {
            $table->dropForeign('fk__survey_condition_tag__condition_tag');
            $table->dropForeign('fk__survey_condition_tag__survey');
        });

        Schema::table('study', function (Blueprint $table) {
            $table->dropForeign('fk__study__default_locale');
        });

        Schema::table('study_respondent', function (Blueprint $table) {
            $table->dropForeign('fk__study_respondent__respondent');
            $table->dropForeign('fk__study_respondent__study');
        });

        Schema::table('study_locale', function (Blueprint $table) {
            $table->dropForeign('fk__study_locale__locale');
            $table->dropForeign('fk__study_locale__study');
        });

        Schema::table('study_form', function (Blueprint $table) {
            $table->dropForeign('fk__study_form__form');
            $table->dropForeign('fk__study_form__study');
        });

        Schema::table('skip_condition_tag', function (Blueprint $table) {
            $table->dropForeign('fk__skip_condition_tag__skip');
        });

        Schema::table('section', function (Blueprint $table) {
            $table->dropForeign('fk__section_name__translation');
        });

        Schema::table('section_question_group', function (Blueprint $table) {
            $table->dropForeign('fk__section_question_group__question_group');
            $table->dropForeign('fk__section_question_group__section');
        });

        Schema::table('section_condition_tag', function (Blueprint $table) {
            $table->dropForeign('fk__section_condition_tag__condition_tag');
            $table->dropForeign('fk__section_condition_tag__section');
            $table->dropForeign('fk__section_condition_tag__survey');
        });

        Schema::table('respondent', function (Blueprint $table) {
            $table->dropForeign('fk__respondent__geo');
        });

        Schema::table('respondent_photo', function (Blueprint $table) {
            $table->dropForeign('fk__respondent_photo__photo');
            $table->dropForeign('fk__respondent_photo__respondent');
        });

        Schema::table('respondent_group_tag', function (Blueprint $table) {
            $table->dropForeign('fk__respondent_group_tag__group_tag');
            $table->dropForeign('fk__respondent_group_tag__respondent');
        });

        Schema::table('respondent_condition_tag', function (Blueprint $table) {
            $table->dropForeign('fk__respondent_condition_tag__condition');
            $table->dropForeign('fk__respondent_condition_tag__respondent');
        });

        Schema::table('question', function (Blueprint $table) {
            $table->dropForeign('fk__question__question_group');
            $table->dropForeign('fk__question__question_type');
            $table->dropForeign('fk__question__translation');
        });

        Schema::table('question_parameter', function (Blueprint $table) {
            $table->dropForeign('fk__question_parameter__parameter');
            $table->dropForeign('fk__question_parameter__question');
        });

        Schema::table('question_group_skip', function (Blueprint $table) {
            $table->dropForeign('fk__question_group_skip__question_group');
            $table->dropForeign('fk__question_group_skip__skip');
        });

        Schema::table('question_choice', function (Blueprint $table) {
            $table->dropForeign('fk__question_choice__choice');
            $table->dropForeign('fk__question_choice__question');
        });

        Schema::table('question_assign_condition_tag', function (Blueprint $table) {
            $table->dropForeign('fk__question_assign_condition_tag__assign_condition_tag');
            $table->dropForeign('fk__question_assign_condition_tag__question');
        });

        Schema::table('photo_tag', function (Blueprint $table) {
            $table->dropForeign('fk__photo_tag__photo');
            $table->dropForeign('fk__photo_tag__tag');
        });

        Schema::table('interview', function (Blueprint $table) {
            $table->dropForeign('fk__survey_session__survey');
            $table->dropForeign('fk__survey_session__user');
        });

        Schema::table('interview_question', function (Blueprint $table) {
            $table->dropForeign('fk__interview_question__interview');
            $table->dropForeign('fk__interview_question__question');
        });

        Schema::table('group_tag', function (Blueprint $table) {
            $table->dropForeign('fk__group_tag__group_tag_type');
        });

        Schema::table('geo_type', function (Blueprint $table) {
            $table->dropForeign('FK__geo_type__study');
        });

        Schema::table('geo', function (Blueprint $table) {
            $table->dropForeign('fk__geo__geo_type');
            $table->dropForeign('fk__geo__parent_geo');
            $table->dropForeign('fk__geo_name__translation');
        });

        Schema::table('geo_photo', function (Blueprint $table) {
            $table->dropForeign('fk__geo_photo__geo');
            $table->dropForeign('fk__geo_photo__photo');
        });

        Schema::table('form', function (Blueprint $table) {
            $table->dropForeign('fk__form_name__translation');
        });

        Schema::table('form_skip', function (Blueprint $table) {
            $table->dropForeign('fk__form_skip__form');
            $table->dropForeign('fk__form_skip__skip');
        });

        Schema::table('form_section', function (Blueprint $table) {
            $table->dropForeign('fk__form_section__form');
            $table->dropForeign('fk__form_section__section');
            $table->dropForeign('fk__form_section_repeat_prompt__translation');
        });

        Schema::table('edge', function (Blueprint $table) {
            $table->dropForeign('fk__edge_list_source__respondent');
            $table->dropForeign('fk__edge_list_target__respondent');
        });

        Schema::table('edge_datum', function (Blueprint $table) {
            $table->dropForeign('fk__connection_datum__connection');
            $table->dropForeign('fk__connection_datum__datum');
        });

        Schema::table('datum', function (Blueprint $table) {
            $table->dropForeign('fk__datum__choice');
            $table->dropForeign('fk__datum__datum_type');
            $table->dropForeign('fk__datum__question');
            $table->dropForeign('fk__datum__survey');
            $table->dropForeign('fk__parent_datum_id__datum');
        });

        Schema::table('datum_photo', function (Blueprint $table) {
            $table->dropForeign('fk__datum_photo__datum');
            $table->dropForeign('fk__datum_photo__photo');
        });

        Schema::table('datum_group_tag', function (Blueprint $table) {
            $table->dropForeign('FK__datum_group_tag__datum');
            $table->dropForeign('FK__datum_group_tag__group_tag');
        });

        Schema::table('datum_geo', function (Blueprint $table) {
            $table->dropForeign('FK__datum_geo__datum');
            $table->dropForeign('FK__datum_geo__geo');
        });

        Schema::table('datum_choice', function (Blueprint $table) {
            $table->dropForeign('FK__datum_choice__choice');
            $table->dropForeign('FK__datum_choice__datum');
        });

        Schema::table('choice', function (Blueprint $table) {
            $table->dropForeign('fk__choice__translation');
        });

        Schema::table('assign_condition_tag', function (Blueprint $table) {
            $table->dropForeign('fk__assign_condition_tag__condition');
        });

        Schema::drop('user');

        Schema::drop('user_study');

        Schema::drop('translation_text');

        Schema::drop('translation');

        Schema::drop('token');

        Schema::drop('tag');

        Schema::drop('survey');

        Schema::drop('survey_condition_tag');

        Schema::drop('study');

        Schema::drop('study_respondent');

        Schema::drop('study_locale');

        Schema::drop('study_form');

        Schema::drop('skip');

        Schema::drop('skip_condition_tag');

        Schema::drop('section');

        Schema::drop('section_question_group');

        Schema::drop('section_condition_tag');

        Schema::drop('respondent');

        Schema::drop('respondent_photo');

        Schema::drop('respondent_group_tag');

        Schema::drop('respondent_condition_tag');

        Schema::drop('question_type');

        Schema::drop('question');

        Schema::drop('question_parameter');

        Schema::drop('question_group');

        Schema::drop('question_group_skip');

        Schema::drop('question_choice');

        Schema::drop('question_assign_condition_tag');

        Schema::drop('photo_tag');

        Schema::drop('photo');

        Schema::drop('parameter');

        Schema::drop('log');

        Schema::drop('locale');

        Schema::drop('key');

        Schema::drop('interview');

        Schema::drop('interview_question');

        Schema::drop('group_tag_type');

        Schema::drop('group_tag');

        Schema::drop('geo_type');

        Schema::drop('geo');

        Schema::drop('geo_photo');

        Schema::drop('form');

        Schema::drop('form_skip');

        Schema::drop('form_section');

        Schema::drop('edge');

        Schema::drop('edge_datum');

        Schema::drop('device');

        Schema::drop('datum_type');

        Schema::drop('datum');

        Schema::drop('datum_roster');

        Schema::drop('datum_photo');

        Schema::drop('datum_group_tag');

        Schema::drop('datum_geo');

        Schema::drop('datum_choice');

        Schema::drop('condition_tag');

        Schema::drop('choice');

        Schema::drop('assign_condition_tag');
    }
}
