<?php

use App\Library\DatabaseHelper;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHardDeleteCascadeToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (DatabaseHelper::foreignKeys() as $foreignKey) {
            Schema::table($foreignKey['table_name'], function (Blueprint $table) use ($foreignKey) {
                $table->dropForeign($foreignKey['constraint_name']);

                $table->foreign($foreignKey['column_name'])
                    ->references($foreignKey['referenced_column_name'])->on($foreignKey['referenced_table_name'])
                    ->onUpdate('no action')
                    ->onDelete('cascade');  // when referenced row is deleted, cascade delete dependent rows
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*
        to retrieve [table => [column => constraint]]:

        remove this and all later migrations from database/migrations directory
        empty database
        run `php artisan migrate` in shell
        run `php artisan tinker` in shell
        paste in the following and press enter:

        array_reduce(App\Library\DatabaseHelper::foreignKeys(), function ($carry, $foreignKey) {
        	array_set($carry, $foreignKey['table_name'] . '.' . $foreignKey['column_name'], $foreignKey['constraint_name']);

        	return $carry;
        }, []);

        replace 7 spaces with 9 spaces and 5 spaces with 12 spaces for proper indentation
        */
        $originalTableColumnConstraints = [
            "assign_condition_tag" => [
                "condition_tag_id" => "fk__assign_condition_tag__condition",
            ],
            "choice" => [
                "choice_translation_id" => "fk__choice__translation",
            ],
            "datum" => [
                "choice_id" => "fk__datum__choice",
                "datum_type_id" => "fk__datum__datum_type",
                "parent_datum_id" => "fk__parent_datum_id__datum",
                "question_id" => "fk__datum__question",
                "survey_id" => "fk__datum__survey",
            ],
            "datum_choice" => [
                "choice_id" => "FK__datum_choice__choice",
                "datum_id" => "FK__datum_choice__datum",
            ],
            "datum_geo" => [
                "datum_id" => "FK__datum_geo__datum",
                "geo_id" => "FK__datum_geo__geo",
            ],
            "datum_group_tag" => [
                "datum_id" => "FK__datum_group_tag__datum",
                "group_tag_id" => "FK__datum_group_tag__group_tag",
            ],
            "datum_photo" => [
                "datum_id" => "fk__datum_photo__datum",
                "photo_id" => "fk__datum_photo__photo",
            ],
            "edge" => [
                "source_respondent_id" => "fk__edge_list_source__respondent",
                "target_respondent_id" => "fk__edge_list_target__respondent",
            ],
            "edge_datum" => [
                "datum_id" => "fk__connection_datum__datum",
                "edge_id" => "fk__connection_datum__connection",
            ],
            "form" => [
                "name_translation_id" => "fk__form_name__translation",
            ],
            "form_section" => [
                "form_id" => "fk__form_section__form",
                "repeat_prompt_translation_id" => "fk__form_section_repeat_prompt__translation",
                "section_id" => "fk__form_section__section",
            ],
            "form_skip" => [
                "form_id" => "fk__form_skip__form",
                "skip_id" => "fk__form_skip__skip",
            ],
            "geo" => [
                "geo_type_id" => "fk__geo__geo_type",
                "name_translation_id" => "fk__geo_name__translation",
                "parent_id" => "fk__geo__parent_geo",
            ],
            "geo_photo" => [
                "geo_id" => "fk__geo_photo__geo",
                "photo_id" => "fk__geo_photo__photo",
            ],
            "geo_type" => [
                "study_id" => "FK__geo_type__study",
            ],
            "group_tag" => [
                "group_tag_type_id" => "fk__group_tag__group_tag_type",
            ],
            "interview" => [
                "survey_id" => "fk__survey_session__survey",
                "user_id" => "fk__survey_session__user",
            ],
            "interview_question" => [
                "interview_id" => "fk__interview_question__interview",
                "question_id" => "fk__interview_question__question",
            ],
            "photo_tag" => [
                "photo_id" => "fk__photo_tag__photo",
                "tag_id" => "fk__photo_tag__tag",
            ],
            "question" => [
                "question_group_id" => "fk__question__question_group",
                "question_translation_id" => "fk__question__translation",
                "question_type_id" => "fk__question__question_type",
            ],
            "question_assign_condition_tag" => [
                "assign_condition_tag_id" => "fk__question_assign_condition_tag__assign_condition_tag",
                "question_id" => "fk__question_assign_condition_tag__question",
            ],
            "question_choice" => [
                "choice_id" => "fk__question_choice__choice",
                "question_id" => "fk__question_choice__question",
            ],
            "question_group_skip" => [
                "question_group_id" => "fk__question_group_skip__question_group",
                "skip_id" => "fk__question_group_skip__skip",
            ],
            "question_parameter" => [
                "parameter_id" => "fk__question_parameter__parameter",
                "question_id" => "fk__question_parameter__question",
            ],
            "respondent" => [
                "geo_id" => "fk__respondent__geo",
            ],
            "respondent_condition_tag" => [
                "condition_tag_id" => "fk__respondent_condition_tag__condition_tag",
                "respondent_id" => "fk__respondent_condition_tag__respondent",
            ],
            "respondent_group_tag" => [
                "group_tag_id" => "fk__respondent_group_tag__group_tag",
                "respondent_id" => "fk__respondent_group_tag__respondent",
            ],
            "respondent_photo" => [
                "photo_id" => "fk__respondent_photo__photo",
                "respondent_id" => "fk__respondent_photo__respondent",
            ],
            "section" => [
                "name_translation_id" => "fk__section_name__translation",
            ],
            "section_condition_tag" => [
                "condition_id" => "fk__section_condition_tag__condition_tag",
                "section_id" => "fk__section_condition_tag__section",
                "survey_id" => "fk__section_condition_tag__survey",
            ],
            "section_question_group" => [
                "question_group_id" => "fk__section_question_group__question_group",
                "section_id" => "fk__section_question_group__section",
            ],
            "skip_condition_tag" => [
                "skip_id" => "fk__skip_condition_tag__skip",
            ],
            "study" => [
                "default_locale_id" => "fk__study__default_locale",
            ],
            "study_form" => [
                "form_master_id" => "fk__study_form__form",
                "study_id" => "fk__study_form__study",
            ],
            "study_locale" => [
                "locale_id" => "fk__study_locale__locale",
                "study_id" => "fk__study_locale__study",
            ],
            "study_respondent" => [
                "respondent_id" => "fk__study_respondent__respondent",
                "study_id" => "fk__study_respondent__study",
            ],
            "survey" => [
                "form_id" => "fk__survey__form",
                "last_question_id" => "fk__survey__last_question",
                "respondent_id" => "fk__survey__respondent",
                "study_id" => "fk__survey__study",
            ],
            "survey_condition_tag" => [
                "condition_id" => "fk__survey_condition_tag__condition_tag",
                "survey_id" => "fk__survey_condition_tag__survey",
            ],
            "translation_text" => [
                "locale_id" => "fk__translation_text__locale",
                "translation_id" => "fk__translation_text__translation",
            ],
            "user" => [
                "selected_study_id" => "fk__user_selected_study__study",
            ],
            "user_study" => [
                "study_id" => "fk__user_study__study",
                "user_id" => "fk__user_study__user",
            ],
        ];

        foreach (DatabaseHelper::foreignKeys() as $foreignKey) {
            Schema::table($foreignKey['table_name'], function (Blueprint $table) use ($originalTableColumnConstraints, $foreignKey) {
                $table->dropForeign($foreignKey['constraint_name']);

                $table->foreign($foreignKey['column_name'], array_get($originalTableColumnConstraints, $foreignKey['table_name'] . '.' . $foreignKey['column_name']))   // prior naming convention except uses table + column rather than table + referenced_table to prevent duplicate key error
                    ->references($foreignKey['referenced_column_name'])->on($foreignKey['referenced_table_name'])
                    ->onUpdate('no action')
                    ->onDelete('no action');
            });
        }
    }
}
