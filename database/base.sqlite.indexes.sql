CREATE INDEX "idx_geo_photo_fk__geo_photo__geo_idx" ON "geo_photo" (`geo_id`);
CREATE INDEX "idx_geo_photo_fk__geo_photo__photo_idx" ON "geo_photo" (`photo_id`);
CREATE INDEX "idx_respondent_name_respondent_name_respondent_id_foreign" ON "respondent_name" (`respondent_id`);
CREATE INDEX "idx_respondent_name_respondent_name_locale_id_foreign" ON "respondent_name" (`locale_id`);
CREATE INDEX "idx_respondent_name_respondent_name_previous_respondent_name_id_foreign" ON "respondent_name" (`previous_respondent_name_id`);
CREATE INDEX "idx_respondent_name_rn_name_idx" ON "respondent_name" (`name`);
CREATE INDEX "idx_respondent_group_tag_fk__respondent_group__respondent_idx" ON "respondent_group_tag" (`respondent_id`);
CREATE INDEX "idx_respondent_group_tag_fk__respondent_group__group_idx" ON "respondent_group_tag" (`group_tag_id`);
CREATE INDEX "idx_translation_text_fk__translation_text__translation_idx" ON "translation_text" (`translation_id`);
CREATE INDEX "idx_translation_text_fk__translation_text__locale_idx" ON "translation_text" (`locale_id`);
CREATE INDEX "idx_edge_datum_fk__edge_datum__edge_idx" ON "edge_datum" (`edge_id`);
CREATE INDEX "idx_edge_datum_fk__edge_datum__datum_idx" ON "edge_datum" (`datum_id`);
CREATE INDEX "idx_section_condition_tag_fk__section_condition__condition_tag_idx" ON "section_condition_tag" (`condition_id`);
CREATE INDEX "idx_section_condition_tag_fk__section_condition_tag__section" ON "section_condition_tag" (`section_id`);
CREATE INDEX "idx_section_condition_tag_fk__section_condition_tag__survey" ON "section_condition_tag" (`survey_id`);
CREATE INDEX "idx_section_condition_tag_fk__follow_up_datum__datum" ON "section_condition_tag" (`follow_up_datum_id`);
CREATE INDEX "idx_question_datum_fk__question_datum__datum_idx" ON "question_datum" (`follow_up_datum_id`);
CREATE INDEX "idx_question_datum_fk__question_datum__question_idx" ON "question_datum" (`question_id`);
CREATE INDEX "idx_question_datum_fk__question_datum__survey_idx" ON "question_datum" (`survey_id`);
CREATE INDEX "idx_question_datum_question_datum_created_at_idx" ON "question_datum" (`created_at`);
CREATE INDEX "idx_study_respondent_fk__study_respondent__study_idx" ON "study_respondent" (`study_id`);
CREATE INDEX "idx_study_respondent_fk__study_respondent__respondent_idx" ON "study_respondent" (`respondent_id`);
CREATE INDEX "idx_survey_condition_tag_fk__survey_condition_tag__survey_idx" ON "survey_condition_tag" (`survey_id`);
CREATE INDEX "idx_survey_condition_tag_fk__interview_condition__condition_idx" ON "survey_condition_tag" (`condition_id`);
CREATE INDEX "idx_question_choice_fk__question_choice__question_idx" ON "question_choice" (`question_id`);
CREATE INDEX "idx_question_choice_fk__question_choice__choice_idx" ON "question_choice" (`choice_id`);
CREATE INDEX "idx_question_fk__question__question_type_idx" ON "question" (`question_type_id`);
CREATE INDEX "idx_question_fk__question__translation_idx" ON "question" (`question_translation_id`);
CREATE INDEX "idx_question_fk__question__question_group_idx" ON "question" (`question_group_id`);
CREATE INDEX "idx_study_parameter_study_parameter_study_id_foreign" ON "study_parameter" (`study_id`);
CREATE INDEX "idx_study_parameter_study_parameter_parameter_id_foreign" ON "study_parameter" (`parameter_id`);
CREATE INDEX "idx_respondent_geo_respondent_geo_geo_id_foreign" ON "respondent_geo" (`geo_id`);
CREATE INDEX "idx_respondent_geo_respondent_geo_previous_respondent_geo_id_foreign" ON "respondent_geo" (`previous_respondent_geo_id`);
CREATE INDEX "idx_respondent_geo_res_geo_is_current_idx" ON "respondent_geo" (`is_current`);
CREATE INDEX "idx_respondent_geo_res_geo_res_id_is_current_idx" ON "respondent_geo" (`respondent_id`,`is_current`);
CREATE INDEX "idx_question_assign_condition_tag_fk__question_assign_condition_tag__question_idx" ON "question_assign_condition_tag" (`question_id`);
CREATE INDEX "idx_question_assign_condition_tag_fk__question_assign_condition_tag__assign_condition_tag_idx" ON "question_assign_condition_tag" (`assign_condition_tag_id`);
CREATE INDEX "idx_respondent_fill_respondent_fill_respondent_id_foreign" ON "respondent_fill" (`respondent_id`);
CREATE INDEX "idx_study_fk__study__default_locale_idx" ON "study" (`default_locale_id`);
CREATE INDEX "idx_study_fk__study_test_study__idx" ON "study" (`test_study_id`);
CREATE INDEX "idx_photo_tag_fk__photo_tag__photo_idx" ON "photo_tag" (`photo_id`);
CREATE INDEX "idx_photo_tag_fk__photo_tag__tag_idx" ON "photo_tag" (`tag_id`);
CREATE INDEX "idx_role_permission_role_permission_role_id_foreign" ON "role_permission" (`role_id`);
CREATE INDEX "idx_role_permission_role_permission_permission_id_foreign" ON "role_permission" (`permission_id`);
CREATE INDEX "idx_study_form_fk__study_form__study_idx" ON "study_form" (`study_id`);
CREATE INDEX "idx_study_form_fk__study_form__form_idx" ON "study_form" (`form_master_id`);
CREATE INDEX "idx_study_form_fk__form_study_form_type_idx" ON "study_form" (`form_type_id`);
CREATE INDEX "idx_study_form_fk__study_form_census_type__idx" ON "study_form" (`census_type_id`);
CREATE INDEX "idx_study_form_fk__geo_type_id_geo_type__idx" ON "study_form" (`geo_type_id`);
CREATE INDEX "idx_study_form_fk__study_form_current_version__idx" ON "study_form" (`current_version_id`);
CREATE INDEX "idx_datum_fk__datum__choice_idx" ON "datum" (`choice_id`);
CREATE INDEX "idx_datum_fk__datum__survey_idx" ON "datum" (`survey_id`);
CREATE INDEX "idx_datum_fk__datum__question_idx" ON "datum" (`question_id`);
CREATE INDEX "idx_datum_fk__parent_datum_id__datum_idx" ON "datum" (`parent_datum_id`);
CREATE INDEX "idx_datum_fk__datum__datum_type_idx" ON "datum" (`datum_type_id`);
CREATE INDEX "idx_datum_datum_roster_id_foreign" ON "datum" (`roster_id`);
CREATE INDEX "idx_datum_datum_question_datum_id_foreign" ON "datum" (`question_datum_id`);
CREATE INDEX "idx_datum_datum_geo_id_foreign" ON "datum" (`geo_id`);
CREATE INDEX "idx_datum_datum_edge_id_foreign" ON "datum" (`edge_id`);
CREATE INDEX "idx_datum_datum_photo_id_foreign" ON "datum" (`photo_id`);
CREATE INDEX "idx_datum_fk__datum_respondent_geo__idx" ON "datum" (`respondent_geo_id`);
CREATE INDEX "idx_datum_fk__datum_respondent_name__idx" ON "datum" (`respondent_name_id`);
CREATE INDEX "idx_datum_fk__datum_action__idx" ON "datum" (`action_id`);
CREATE INDEX "idx_interview_fk__survey_session__survey_idx" ON "interview" (`survey_id`);
CREATE INDEX "idx_interview_fk__survey_session__user_idx" ON "interview" (`user_id`);
CREATE INDEX "idx_form_skip_fk__form_skip__form_idx" ON "form_skip" (`form_id`);
CREATE INDEX "idx_form_skip_fk__form_skip__skip_idx" ON "form_skip" (`skip_id`);
CREATE INDEX "idx_action_action_question_id_foreign" ON "action" (`question_id`);
CREATE INDEX "idx_action_action_survey_id_foreign" ON "action" (`survey_id`);
CREATE INDEX "idx_action_action_interview_id_foreign" ON "action" (`interview_id`);
CREATE INDEX "idx_action_fk__preload_preload_action__idx" ON "action" (`preload_action_id`);
CREATE INDEX "idx_action_fk__action_follow_up_action__idx" ON "action" (`follow_up_action_id`);
CREATE INDEX "idx_preload_action_preload_action_respondent_id_foreign" ON "preload_action" (`respondent_id`);
CREATE INDEX "idx_preload_action_preload_action_question_id_foreign" ON "preload_action" (`question_id`);
CREATE INDEX "idx_preload_preload_respondent_id_foreign" ON "preload" (`respondent_id`);
CREATE INDEX "idx_preload_preload_form_id_foreign" ON "preload" (`form_id`);
CREATE INDEX "idx_preload_preload_study_id_foreign" ON "preload" (`study_id`);
CREATE INDEX "idx_preload_preload_last_question_id_foreign" ON "preload" (`last_question_id`);
CREATE INDEX "idx_edge_fk__edge_source_respondent_id__respondent_idx" ON "edge" (`source_respondent_id`);
CREATE INDEX "idx_edge_fk__edge_target_respondent_id__respondent_idx" ON "edge" (`target_respondent_id`);
CREATE INDEX "idx_edge_idx_edge_note" ON "edge" (`note`);
CREATE INDEX "idx_form_section_fk__form_section__form_idx" ON "form_section" (`form_id`);
CREATE INDEX "idx_form_section_fk__form_section__section_idx" ON "form_section" (`section_id`);
CREATE INDEX "idx_form_section_fk__form_section_repeat_prompt__translation_idx" ON "form_section" (`repeat_prompt_translation_id`);
CREATE INDEX "idx_form_section_fk__follow_up_question__question" ON "form_section" (`follow_up_question_id`);
CREATE INDEX "idx_question_parameter_fk__question_parameter__question_idx" ON "question_parameter" (`question_id`);
CREATE INDEX "idx_question_parameter_fk__question_parameter__parameter_idx" ON "question_parameter" (`parameter_id`);
CREATE INDEX "idx_group_tag_fk__group__group_type_idx" ON "group_tag" (`group_tag_type_id`);
CREATE INDEX "idx_user_study_fk__user_study__user_idx" ON "user_study" (`user_id`);
CREATE INDEX "idx_user_study_fk__user_study__study_idx" ON "user_study" (`study_id`);
CREATE INDEX "idx_datum_choice_FK__datum_choice__datum_idx" ON "datum_choice" (`datum_id`);
CREATE INDEX "idx_datum_choice_FK__datum_choice__choice_idx" ON "datum_choice" (`choice_id`);
CREATE INDEX "idx_section_fk__section_name__translation_idx" ON "section" (`name_translation_id`);
CREATE INDEX "idx_study_locale_fk__study_locale__study_idx" ON "study_locale" (`study_id`);
CREATE INDEX "idx_study_locale_fk__study_locale__locale_idx" ON "study_locale" (`locale_id`);
CREATE INDEX "idx_datum_group_tag_FK__datum_group_tag__datum_idx" ON "datum_group_tag" (`datum_id`);
CREATE INDEX "idx_datum_group_tag_FK__datum_group_tag__group_tag_idx" ON "datum_group_tag" (`group_tag_id`);
CREATE INDEX "idx_datum_geo_FK__datum_geo__datum_idx" ON "datum_geo" (`datum_id`);
CREATE INDEX "idx_datum_geo_FK__datum_geo__geo_idx" ON "datum_geo" (`geo_id`);
CREATE INDEX "idx_form_idx__form_master_id" ON "form" (`form_master_id`);
CREATE INDEX "idx_form_fk__form_name__translation_idx" ON "form" (`name_translation_id`);
CREATE INDEX "idx_skip_condition_tag_fk__skip_condition_tag__skip_idx" ON "skip_condition_tag" (`skip_id`);
CREATE INDEX "idx_survey_fk__survey__respondent_idx" ON "survey" (`respondent_id`);
CREATE INDEX "idx_survey_fk__survey__form_idx" ON "survey" (`form_id`);
CREATE INDEX "idx_survey_fk__survey__study_idx" ON "survey" (`study_id`);
CREATE INDEX "idx_survey_fk__survey__last_question_idx" ON "survey" (`last_question_id`);
CREATE INDEX "idx_survey_survey_created_at_idx" ON "survey" (`created_at`);
CREATE INDEX "idx_respondent_photo_fk__respondent_photo__respondent_idx" ON "respondent_photo" (`respondent_id`);
CREATE INDEX "idx_respondent_photo_fk__respondent_photo__photo_idx" ON "respondent_photo" (`photo_id`);
CREATE INDEX "idx_condition_tag_ct_name_idx" ON "condition_tag" (`name`);
CREATE INDEX "idx_question_group_skip_fk__question_group_skip__question_group_idx" ON "question_group_skip" (`question_group_id`);
CREATE INDEX "idx_question_group_skip_fk__question_group__skip_idx" ON "question_group_skip" (`skip_id`);
CREATE INDEX "idx_assign_condition_tag_fk__assign_condition_tag__condition_idx" ON "assign_condition_tag" (`condition_tag_id`);
CREATE INDEX "idx_section_skip_section_skip_section_id_foreign" ON "section_skip" (`section_id`);
CREATE INDEX "idx_section_skip_section_skip_skip_id_foreign" ON "section_skip" (`skip_id`);
CREATE INDEX "idx_respondent_condition_tag_fk__respondent_condition__respondent_idx" ON "respondent_condition_tag" (`respondent_id`);
CREATE INDEX "idx_respondent_condition_tag_fk__respondent_condition_tag__condition_tag" ON "respondent_condition_tag" (`condition_tag_id`);
CREATE INDEX "idx_self_administered_survey_self_administered_survey_survey_id_foreign" ON "self_administered_survey" (`survey_id`);
CREATE INDEX "idx_geo_fk__geo__geo_type_idx" ON "geo" (`geo_type_id`);
CREATE INDEX "idx_geo_fk__geo__parent_geo_idx" ON "geo" (`parent_id`);
CREATE INDEX "idx_geo_fk__name_translation__translation_idx" ON "geo" (`name_translation_id`);
CREATE INDEX "idx_choice_fk__choice__translation_idx" ON "choice" (`choice_translation_id`);
CREATE INDEX "idx_section_question_group_fk__form_question__form_idx" ON "section_question_group" (`section_id`);
CREATE INDEX "idx_section_question_group_fk__section_question_group__question_group_idx" ON "section_question_group" (`question_group_id`);
CREATE INDEX "idx_user_fk__user_selected_study__study_idx" ON "user" (`selected_study_id`);
CREATE INDEX "idx_user_fk__user_role__idx" ON "user" (`role_id`);
CREATE INDEX "idx_datum_photo_fk__datum_photo__datum_idx" ON "datum_photo" (`datum_id`);
CREATE INDEX "idx_datum_photo_fk__datum_photo__photo_idx" ON "datum_photo" (`photo_id`);
CREATE INDEX "idx_respondent_fk__respondent__geo_idx" ON "respondent" (`geo_id`);
CREATE INDEX "idx_respondent_fk__respondent_associated_respondent__idx" ON "respondent" (`associated_respondent_id`);
CREATE INDEX "idx_interview_question_fk__interview_question__survey_session_idx" ON "interview_question" (`interview_id`);
CREATE INDEX "idx_interview_question_fk__interview_question__question_idx" ON "interview_question" (`question_id`);
CREATE INDEX "idx_geo_type_FK__geo_type__study_idx" ON "geo_type" (`study_id`);
