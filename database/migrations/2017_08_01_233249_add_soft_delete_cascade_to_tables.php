<?php

use App\Library\DatabaseHelper;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftDeleteCascadeToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (DatabaseHelper::foreignKeys() as $foreignKey) {
            DatabaseHelper::createSoftDeleteTrigger($foreignKey['table_name'], $foreignKey['column_name'], $foreignKey['referenced_table_name'], $foreignKey['referenced_column_name']);
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
        to retrieve [["table_name" => table_name, "column_name" => column_name, "referenced_table_name" => referenced_table_name, "referenced_column_name" => referenced_column_name]]:

        remove this and all later migrations from database/migrations directory
        empty database
        run `php artisan migrate` in shell
        run `php artisan tinker` in shell
        paste in the following and press enter:

        array_map(function ($foreignKey) {
        	return array_intersect_key($foreignKey, array_flip(['table_name', 'column_name', 'referenced_table_name', 'referenced_column_name']));
        }, App\Library\DatabaseHelper::foreignKeys())

        replace 7 spaces with 9 spaces and 5 spaces with 12 spaces for proper indentation
        */
        $originalForeignKeys = [
            [
                "table_name" => "assign_condition_tag",
                "column_name" => "condition_tag_id",
                "referenced_table_name" => "condition_tag",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "choice",
                "column_name" => "choice_translation_id",
                "referenced_table_name" => "translation",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "datum",
                "column_name" => "choice_id",
                "referenced_table_name" => "choice",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "datum",
                "column_name" => "datum_type_id",
                "referenced_table_name" => "datum_type",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "datum",
                "column_name" => "parent_datum_id",
                "referenced_table_name" => "datum",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "datum",
                "column_name" => "question_id",
                "referenced_table_name" => "question",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "datum",
                "column_name" => "survey_id",
                "referenced_table_name" => "survey",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "datum_choice",
                "column_name" => "choice_id",
                "referenced_table_name" => "choice",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "datum_choice",
                "column_name" => "datum_id",
                "referenced_table_name" => "datum",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "datum_geo",
                "column_name" => "datum_id",
                "referenced_table_name" => "datum",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "datum_geo",
                "column_name" => "geo_id",
                "referenced_table_name" => "geo",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "datum_group_tag",
                "column_name" => "datum_id",
                "referenced_table_name" => "datum",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "datum_group_tag",
                "column_name" => "group_tag_id",
                "referenced_table_name" => "group_tag",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "datum_photo",
                "column_name" => "datum_id",
                "referenced_table_name" => "datum",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "datum_photo",
                "column_name" => "photo_id",
                "referenced_table_name" => "photo",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "edge",
                "column_name" => "source_respondent_id",
                "referenced_table_name" => "respondent",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "edge",
                "column_name" => "target_respondent_id",
                "referenced_table_name" => "respondent",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "edge_datum",
                "column_name" => "datum_id",
                "referenced_table_name" => "datum",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "edge_datum",
                "column_name" => "edge_id",
                "referenced_table_name" => "edge",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "form",
                "column_name" => "name_translation_id",
                "referenced_table_name" => "translation",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "form_section",
                "column_name" => "form_id",
                "referenced_table_name" => "form",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "form_section",
                "column_name" => "repeat_prompt_translation_id",
                "referenced_table_name" => "translation",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "form_section",
                "column_name" => "section_id",
                "referenced_table_name" => "section",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "form_skip",
                "column_name" => "form_id",
                "referenced_table_name" => "form",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "form_skip",
                "column_name" => "skip_id",
                "referenced_table_name" => "skip",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "geo",
                "column_name" => "geo_type_id",
                "referenced_table_name" => "geo_type",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "geo",
                "column_name" => "name_translation_id",
                "referenced_table_name" => "translation",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "geo",
                "column_name" => "parent_id",
                "referenced_table_name" => "geo",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "geo_photo",
                "column_name" => "geo_id",
                "referenced_table_name" => "geo",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "geo_photo",
                "column_name" => "photo_id",
                "referenced_table_name" => "photo",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "geo_type",
                "column_name" => "study_id",
                "referenced_table_name" => "study",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "group_tag",
                "column_name" => "group_tag_type_id",
                "referenced_table_name" => "group_tag_type",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "interview",
                "column_name" => "survey_id",
                "referenced_table_name" => "survey",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "interview",
                "column_name" => "user_id",
                "referenced_table_name" => "user",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "interview_question",
                "column_name" => "interview_id",
                "referenced_table_name" => "interview",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "interview_question",
                "column_name" => "question_id",
                "referenced_table_name" => "question",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "photo_tag",
                "column_name" => "photo_id",
                "referenced_table_name" => "photo",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "photo_tag",
                "column_name" => "tag_id",
                "referenced_table_name" => "tag",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "question",
                "column_name" => "question_group_id",
                "referenced_table_name" => "question_group",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "question",
                "column_name" => "question_translation_id",
                "referenced_table_name" => "translation",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "question",
                "column_name" => "question_type_id",
                "referenced_table_name" => "question_type",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "question_assign_condition_tag",
                "column_name" => "assign_condition_tag_id",
                "referenced_table_name" => "assign_condition_tag",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "question_assign_condition_tag",
                "column_name" => "question_id",
                "referenced_table_name" => "question",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "question_choice",
                "column_name" => "choice_id",
                "referenced_table_name" => "choice",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "question_choice",
                "column_name" => "question_id",
                "referenced_table_name" => "question",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "question_group_skip",
                "column_name" => "question_group_id",
                "referenced_table_name" => "question_group",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "question_group_skip",
                "column_name" => "skip_id",
                "referenced_table_name" => "skip",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "question_parameter",
                "column_name" => "parameter_id",
                "referenced_table_name" => "parameter",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "question_parameter",
                "column_name" => "question_id",
                "referenced_table_name" => "question",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "respondent",
                "column_name" => "geo_id",
                "referenced_table_name" => "geo",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "respondent_condition_tag",
                "column_name" => "condition_tag_id",
                "referenced_table_name" => "condition_tag",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "respondent_condition_tag",
                "column_name" => "respondent_id",
                "referenced_table_name" => "respondent",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "respondent_group_tag",
                "column_name" => "group_tag_id",
                "referenced_table_name" => "group_tag",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "respondent_group_tag",
                "column_name" => "respondent_id",
                "referenced_table_name" => "respondent",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "respondent_photo",
                "column_name" => "photo_id",
                "referenced_table_name" => "photo",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "respondent_photo",
                "column_name" => "respondent_id",
                "referenced_table_name" => "respondent",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "section",
                "column_name" => "name_translation_id",
                "referenced_table_name" => "translation",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "section_condition_tag",
                "column_name" => "condition_id",
                "referenced_table_name" => "condition_tag",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "section_condition_tag",
                "column_name" => "section_id",
                "referenced_table_name" => "section",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "section_condition_tag",
                "column_name" => "survey_id",
                "referenced_table_name" => "survey",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "section_question_group",
                "column_name" => "question_group_id",
                "referenced_table_name" => "question_group",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "section_question_group",
                "column_name" => "section_id",
                "referenced_table_name" => "section",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "skip_condition_tag",
                "column_name" => "skip_id",
                "referenced_table_name" => "skip",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "study",
                "column_name" => "default_locale_id",
                "referenced_table_name" => "locale",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "study_form",
                "column_name" => "form_master_id",
                "referenced_table_name" => "form",
                "referenced_column_name" => "form_master_id",
            ],
            [
                "table_name" => "study_form",
                "column_name" => "study_id",
                "referenced_table_name" => "study",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "study_locale",
                "column_name" => "locale_id",
                "referenced_table_name" => "locale",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "study_locale",
                "column_name" => "study_id",
                "referenced_table_name" => "study",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "study_respondent",
                "column_name" => "respondent_id",
                "referenced_table_name" => "respondent",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "study_respondent",
                "column_name" => "study_id",
                "referenced_table_name" => "study",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "survey",
                "column_name" => "form_id",
                "referenced_table_name" => "form",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "survey",
                "column_name" => "last_question_id",
                "referenced_table_name" => "question",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "survey",
                "column_name" => "respondent_id",
                "referenced_table_name" => "respondent",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "survey",
                "column_name" => "study_id",
                "referenced_table_name" => "study",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "survey_condition_tag",
                "column_name" => "condition_id",
                "referenced_table_name" => "condition_tag",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "survey_condition_tag",
                "column_name" => "survey_id",
                "referenced_table_name" => "survey",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "translation_text",
                "column_name" => "locale_id",
                "referenced_table_name" => "locale",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "translation_text",
                "column_name" => "translation_id",
                "referenced_table_name" => "translation",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "user",
                "column_name" => "selected_study_id",
                "referenced_table_name" => "study",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "user_study",
                "column_name" => "study_id",
                "referenced_table_name" => "study",
                "referenced_column_name" => "id",
            ],
            [
                "table_name" => "user_study",
                "column_name" => "user_id",
                "referenced_table_name" => "user",
                "referenced_column_name" => "id",
            ],
        ];

        foreach ($originalForeignKeys as $foreignKey) {
            DatabaseHelper::dropSoftDeleteTrigger($foreignKey['table_name'], $foreignKey['column_name'], $foreignKey['referenced_table_name'], $foreignKey['referenced_column_name']);
        }
    }
}
