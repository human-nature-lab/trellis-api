PRAGMA synchronous = OFF;
PRAGMA journal_mode = MEMORY;
BEGIN TRANSACTION;
CREATE TABLE `action` (
  `id` varchar(41) NOT NULL
,  `survey_id` varchar(41) DEFAULT NULL
,  `question_id` varchar(41) DEFAULT NULL
,  `created_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  `payload` text COLLATE BINARY
,  `action_type` varchar(255) NOT NULL
,  `interview_id` varchar(41) NOT NULL
,  `section_follow_up_repetition` integer DEFAULT NULL
,  `section_repetition` integer DEFAULT NULL
,  `preload_action_id` varchar(41) DEFAULT NULL
,  `follow_up_action_id` varchar(41) DEFAULT NULL
,  `random_sort_order` integer NOT NULL
,  `sort_order` integer NOT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `action_interview_id_foreign` FOREIGN KEY (`interview_id`) REFERENCES `interview` (`id`)
,  CONSTRAINT `action_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`)
,  CONSTRAINT `action_survey_id_foreign` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`)
,  CONSTRAINT `fk__action_follow_up_action__idx` FOREIGN KEY (`follow_up_action_id`) REFERENCES `action` (`id`)
,  CONSTRAINT `fk__preload_preload_action__idx` FOREIGN KEY (`preload_action_id`) REFERENCES `preload_action` (`id`)
);
CREATE TABLE `assign_condition_tag` (
  `id` varchar(41) NOT NULL
,  `condition_tag_id` varchar(41) NOT NULL
,  `logic` text NOT NULL
,  `scope` varchar(64) DEFAULT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__assign_condition_tag__condition` FOREIGN KEY (`condition_tag_id`) REFERENCES `condition_tag` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `census_type` (
  `id` varchar(41) NOT NULL
,  `name` varchar(255) NOT NULL
,  PRIMARY KEY (`id`)
);
CREATE TABLE `choice` (
  `id` varchar(41) NOT NULL
,  `choice_translation_id` varchar(41) NOT NULL
,  `val` varchar(255) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__choice__translation` FOREIGN KEY (`choice_translation_id`) REFERENCES `translation` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `condition_tag` (
  `id` varchar(41) NOT NULL
,  `name` varchar(255) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
);
CREATE TABLE `config` (
  `key` varchar(255) NOT NULL
,  `value` varchar(255) DEFAULT NULL
,  `type` varchar(255) NOT NULL DEFAULT 'string'
,  `is_public` integer NOT NULL DEFAULT '0'
,  `default_value` varchar(255) DEFAULT NULL
,  `created_at` timestamp NULL DEFAULT NULL
,  `updated_at` timestamp NULL DEFAULT NULL
,  PRIMARY KEY (`key`)
);
CREATE TABLE `datum` (
  `id` varchar(41) NOT NULL
,  `name` varchar(255) NOT NULL
,  `val` text NOT NULL
,  `choice_id` varchar(41) DEFAULT NULL
,  `survey_id` varchar(41) DEFAULT NULL
,  `question_id` varchar(41) DEFAULT NULL
,  `parent_datum_id` varchar(41) DEFAULT NULL
,  `datum_type_id` varchar(41) NOT NULL DEFAULT '0'
,  `sort_order` integer  DEFAULT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  `roster_id` varchar(41) DEFAULT NULL
,  `event_order` integer NOT NULL
,  `question_datum_id` varchar(41) NOT NULL
,  `geo_id` varchar(41) DEFAULT NULL
,  `edge_id` varchar(41) DEFAULT NULL
,  `photo_id` varchar(41) DEFAULT NULL
,  `respondent_geo_id` varchar(255) DEFAULT NULL
,  `respondent_name_id` varchar(255) DEFAULT NULL
,  `action_id` varchar(41) DEFAULT NULL
,  `random_sort_order` integer NOT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `datum_edge_id_foreign` FOREIGN KEY (`edge_id`) REFERENCES `edge` (`id`)
,  CONSTRAINT `datum_geo_id_foreign` FOREIGN KEY (`geo_id`) REFERENCES `geo` (`id`)
,  CONSTRAINT `datum_photo_id_foreign` FOREIGN KEY (`photo_id`) REFERENCES `photo` (`id`)
,  CONSTRAINT `datum_question_datum_id_foreign` FOREIGN KEY (`question_datum_id`) REFERENCES `question_datum` (`id`)
,  CONSTRAINT `datum_roster_id_foreign` FOREIGN KEY (`roster_id`) REFERENCES `roster` (`id`)
,  CONSTRAINT `fk__datum__choice` FOREIGN KEY (`choice_id`) REFERENCES `choice` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
,  CONSTRAINT `fk__datum__datum_type` FOREIGN KEY (`datum_type_id`) REFERENCES `datum_type` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__datum__question` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
,  CONSTRAINT `fk__datum__survey` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__datum_action__idx` FOREIGN KEY (`action_id`) REFERENCES `action` (`id`)
,  CONSTRAINT `fk__datum_respondent_geo__idx` FOREIGN KEY (`respondent_geo_id`) REFERENCES `respondent_geo` (`id`)
,  CONSTRAINT `fk__datum_respondent_name__idx` FOREIGN KEY (`respondent_name_id`) REFERENCES `respondent_name` (`id`)
,  CONSTRAINT `fk__parent_datum_id__datum` FOREIGN KEY (`parent_datum_id`) REFERENCES `datum` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
);
CREATE TABLE `datum_choice` (
  `id` varchar(41) NOT NULL
,  `datum_id` varchar(41) NOT NULL
,  `choice_id` varchar(41) NOT NULL
,  `sort_order` integer  NOT NULL DEFAULT '0'
,  `override_val` text COLLATE BINARY
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `FK__datum_choice__choice` FOREIGN KEY (`choice_id`) REFERENCES `choice` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `FK__datum_choice__datum` FOREIGN KEY (`datum_id`) REFERENCES `datum` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `datum_geo` (
  `id` varchar(41) NOT NULL
,  `datum_id` varchar(41) NOT NULL
,  `geo_id` varchar(41) NOT NULL
,  `sort_order` integer  NOT NULL DEFAULT '0'
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `FK__datum_geo__datum` FOREIGN KEY (`datum_id`) REFERENCES `datum` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `FK__datum_geo__geo` FOREIGN KEY (`geo_id`) REFERENCES `geo` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `datum_group_tag` (
  `id` varchar(41) NOT NULL
,  `datum_id` varchar(41) NOT NULL
,  `group_tag_id` varchar(41) NOT NULL
,  `sort_order` integer  NOT NULL DEFAULT '0'
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `FK__datum_group_tag__datum` FOREIGN KEY (`datum_id`) REFERENCES `datum` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `FK__datum_group_tag__group_tag` FOREIGN KEY (`group_tag_id`) REFERENCES `group_tag` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `datum_photo` (
  `id` varchar(41) NOT NULL
,  `datum_id` varchar(41) NOT NULL
,  `photo_id` varchar(41) NOT NULL
,  `sort_order` integer  NOT NULL DEFAULT '0'
,  `notes` text COLLATE BINARY
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__datum_photo__datum` FOREIGN KEY (`datum_id`) REFERENCES `datum` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__datum_photo__photo` FOREIGN KEY (`photo_id`) REFERENCES `photo` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `datum_type` (
  `id` varchar(41) NOT NULL
,  `name` varchar(255) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
);
CREATE TABLE `edge` (
  `id` varchar(41) NOT NULL
,  `source_respondent_id` varchar(41) NOT NULL
,  `target_respondent_id` varchar(41) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  `note` varchar(255) NOT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__edge_list_source__respondent` FOREIGN KEY (`source_respondent_id`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__edge_list_target__respondent` FOREIGN KEY (`target_respondent_id`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `edge_datum` (
  `id` varchar(41) NOT NULL
,  `edge_id` varchar(41) NOT NULL
,  `datum_id` varchar(41) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__connection_datum__connection` FOREIGN KEY (`edge_id`) REFERENCES `edge` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__connection_datum__datum` FOREIGN KEY (`datum_id`) REFERENCES `datum` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `form` (
  `id` varchar(41) NOT NULL
,  `form_master_id` varchar(41) NOT NULL
,  `name_translation_id` varchar(41) NOT NULL
,  `version` integer  NOT NULL DEFAULT '0'
,  `is_published` integer NOT NULL DEFAULT '0'
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__form_name__translation` FOREIGN KEY (`name_translation_id`) REFERENCES `translation` (`id`) ON UPDATE NO ACTION
);
CREATE TABLE `form_section` (
  `id` varchar(41) NOT NULL
,  `form_id` varchar(41) DEFAULT NULL
,  `section_id` varchar(41) NOT NULL
,  `sort_order` integer  NOT NULL DEFAULT '0'
,  `is_repeatable` integer NOT NULL DEFAULT '0'
,  `max_repetitions` integer  NOT NULL DEFAULT '0'
,  `repeat_prompt_translation_id` varchar(41) DEFAULT NULL
,  `follow_up_question_id` varchar(41) DEFAULT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  `randomize_follow_up` integer NOT NULL DEFAULT '0'
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__follow_up_question__question` FOREIGN KEY (`follow_up_question_id`) REFERENCES `question` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
,  CONSTRAINT `fk__form_section__form` FOREIGN KEY (`form_id`) REFERENCES `form` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__form_section__section` FOREIGN KEY (`section_id`) REFERENCES `section` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__form_section_repeat_prompt__translation` FOREIGN KEY (`repeat_prompt_translation_id`) REFERENCES `translation` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
);
CREATE TABLE `form_skip` (
  `id` varchar(41) NOT NULL
,  `form_id` varchar(41) NOT NULL
,  `skip_id` varchar(41) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__form_skip__form` FOREIGN KEY (`form_id`) REFERENCES `form` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__form_skip__skip` FOREIGN KEY (`skip_id`) REFERENCES `skip` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `form_type` (
  `id` integer NOT NULL
,  `name` varchar(255) NOT NULL
,  PRIMARY KEY (`id`)
);
CREATE TABLE `geo` (
  `id` varchar(41) NOT NULL
,  `geo_type_id` varchar(41) NOT NULL
,  `parent_id` varchar(41) DEFAULT NULL
,  `latitude` double DEFAULT NULL
,  `longitude` double DEFAULT NULL
,  `altitude` double DEFAULT NULL
,  `name_translation_id` varchar(41) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  `assigned_id` varchar(255) DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__geo__geo_type` FOREIGN KEY (`geo_type_id`) REFERENCES `geo_type` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__geo__parent_geo` FOREIGN KEY (`parent_id`) REFERENCES `geo` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__geo_name__translation` FOREIGN KEY (`name_translation_id`) REFERENCES `translation` (`id`) ON UPDATE NO ACTION
);
CREATE TABLE `geo_photo` (
  `id` varchar(41) NOT NULL
,  `geo_id` varchar(41) NOT NULL
,  `photo_id` varchar(41) NOT NULL
,  `sort_order` integer  NOT NULL
,  `notes` text COLLATE BINARY
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__geo_photo__geo` FOREIGN KEY (`geo_id`) REFERENCES `geo` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__geo_photo__photo` FOREIGN KEY (`photo_id`) REFERENCES `photo` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `geo_type` (
  `id` varchar(41) NOT NULL
,  `parent_id` varchar(41) DEFAULT NULL
,  `study_id` varchar(41) NOT NULL
,  `name` varchar(255) NOT NULL
,  `can_user_add` integer NOT NULL DEFAULT '0'
,  `can_user_add_child` integer NOT NULL DEFAULT '0'
,  `can_contain_respondent` integer NOT NULL DEFAULT '0'
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  `zoom_level` decimal(6,3) DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `FK__geo_type__study` FOREIGN KEY (`study_id`) REFERENCES `study` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `group_tag` (
  `id` varchar(41) NOT NULL
,  `group_tag_type_id` varchar(41) NOT NULL
,  `name` varchar(255) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__group_tag__group_tag_type` FOREIGN KEY (`group_tag_type_id`) REFERENCES `group_tag_type` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `group_tag_type` (
  `id` varchar(41) NOT NULL
,  `name` varchar(255) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
);
CREATE TABLE `interview` (
  `id` varchar(41) NOT NULL
,  `survey_id` varchar(41) NOT NULL
,  `user_id` varchar(41) DEFAULT NULL
,  `start_time` datetime NOT NULL
,  `end_time` datetime DEFAULT NULL
,  `latitude` varchar(45) DEFAULT NULL
,  `longitude` varchar(45) DEFAULT NULL
,  `altitude` varchar(45) DEFAULT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__survey_session__survey` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__survey_session__user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
);
CREATE TABLE `interview_question` (
  `id` varchar(41) NOT NULL
,  `interview_id` varchar(41) NOT NULL
,  `question_id` varchar(41) NOT NULL
,  `enter_date` datetime DEFAULT NULL
,  `answer_date` datetime DEFAULT NULL
,  `leave_date` datetime DEFAULT NULL
,  `elapsed_time` integer  DEFAULT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__interview_question__interview` FOREIGN KEY (`interview_id`) REFERENCES `interview` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__interview_question__question` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `locale` (
  `id` varchar(41) NOT NULL
,  `language_tag` varchar(255) DEFAULT NULL
,  `language_name` varchar(64) DEFAULT NULL
,  `language_native` varchar(64) DEFAULT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
);
CREATE TABLE `parameter` (
  `id` varchar(41) NOT NULL
,  `name` varchar(255) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  `type` varchar(255) DEFAULT NULL
,  PRIMARY KEY (`id`)
);
CREATE TABLE `permission` (
  `id` varchar(255) NOT NULL
,  `type` varchar(255) NOT NULL
,  `description` varchar(255) DEFAULT NULL
,  PRIMARY KEY (`id`)
);
CREATE TABLE `photo` (
  `id` varchar(41) NOT NULL
,  `file_name` varchar(255) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
);
CREATE TABLE `photo_tag` (
  `id` varchar(41) NOT NULL
,  `photo_id` varchar(41) NOT NULL
,  `tag_id` varchar(41) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__photo_tag__photo` FOREIGN KEY (`photo_id`) REFERENCES `photo` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__photo_tag__tag` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `preload` (
  `id` varchar(41) NOT NULL
,  `respondent_id` varchar(41) NOT NULL
,  `form_id` varchar(41) NOT NULL
,  `study_id` varchar(41) NOT NULL
,  `last_question_id` varchar(41) DEFAULT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  `completed_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `preload_form_id_foreign` FOREIGN KEY (`form_id`) REFERENCES `form` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `preload_last_question_id_foreign` FOREIGN KEY (`last_question_id`) REFERENCES `question` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
,  CONSTRAINT `preload_respondent_id_foreign` FOREIGN KEY (`respondent_id`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `preload_study_id_foreign` FOREIGN KEY (`study_id`) REFERENCES `study` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `preload_action` (
  `id` varchar(41) NOT NULL
,  `action_type` varchar(255) NOT NULL
,  `payload` json DEFAULT NULL
,  `respondent_id` varchar(41) NOT NULL
,  `question_id` varchar(41) NOT NULL
,  `created_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `preload_action_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`)
,  CONSTRAINT `preload_action_respondent_id_foreign` FOREIGN KEY (`respondent_id`) REFERENCES `respondent` (`id`)
);
CREATE TABLE `question` (
  `id` varchar(41) NOT NULL
,  `question_type_id` varchar(41) NOT NULL
,  `question_translation_id` varchar(41) NOT NULL
,  `question_group_id` varchar(41) NOT NULL
,  `sort_order` integer  NOT NULL DEFAULT '0'
,  `var_name` varchar(255) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__question__question_group` FOREIGN KEY (`question_group_id`) REFERENCES `question_group` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__question__question_type` FOREIGN KEY (`question_type_id`) REFERENCES `question_type` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__question__translation` FOREIGN KEY (`question_translation_id`) REFERENCES `translation` (`id`) ON UPDATE NO ACTION
);
CREATE TABLE `question_assign_condition_tag` (
  `id` varchar(41) NOT NULL
,  `question_id` varchar(41) NOT NULL
,  `assign_condition_tag_id` varchar(41) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__question_assign_condition_tag__assign_condition_tag` FOREIGN KEY (`assign_condition_tag_id`) REFERENCES `assign_condition_tag` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__question_assign_condition_tag__question` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `question_choice` (
  `id` varchar(41) NOT NULL
,  `question_id` varchar(41) NOT NULL
,  `choice_id` varchar(41) NOT NULL
,  `sort_order` integer  NOT NULL DEFAULT '0'
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__question_choice__choice` FOREIGN KEY (`choice_id`) REFERENCES `choice` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__question_choice__question` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `question_datum` (
  `id` varchar(41) NOT NULL
,  `section_repetition` integer NOT NULL
,  `follow_up_datum_id` varchar(41) DEFAULT NULL
,  `question_id` varchar(41) NOT NULL
,  `survey_id` varchar(41) DEFAULT NULL
,  `answered_at` datetime DEFAULT NULL
,  `skipped_at` datetime DEFAULT NULL
,  `dk_rf` integer DEFAULT NULL
,  `dk_rf_val` text COLLATE BINARY
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  `no_one` integer DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `question_datum_follow_up_datum_id_foreign` FOREIGN KEY (`follow_up_datum_id`) REFERENCES `datum` (`id`)
,  CONSTRAINT `question_datum_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`)
,  CONSTRAINT `question_datum_survey_id_foreign` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`)
);
CREATE TABLE `question_group` (
  `id` varchar(41) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
);
CREATE TABLE `question_group_skip` (
  `id` varchar(41) NOT NULL
,  `question_group_id` varchar(41) NOT NULL
,  `skip_id` varchar(41) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__question_group_skip__question_group` FOREIGN KEY (`question_group_id`) REFERENCES `question_group` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__question_group_skip__skip` FOREIGN KEY (`skip_id`) REFERENCES `skip` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `question_parameter` (
  `id` varchar(41) NOT NULL
,  `question_id` varchar(41) NOT NULL
,  `parameter_id` varchar(41) NOT NULL
,  `val` varchar(255) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__question_parameter__parameter` FOREIGN KEY (`parameter_id`) REFERENCES `parameter` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__question_parameter__question` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `question_type` (
  `id` varchar(41) NOT NULL
,  `name` varchar(255) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
);
CREATE TABLE `respondent` (
  `id` varchar(41) NOT NULL
,  `assigned_id` varchar(255) DEFAULT NULL
,  `geo_id` varchar(41) DEFAULT NULL
,  `notes` text COLLATE BINARY
,  `geo_notes` text COLLATE BINARY
,  `name` varchar(255) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  `associated_respondent_id` varchar(41) DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__respondent__geo` FOREIGN KEY (`geo_id`) REFERENCES `geo` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
,  CONSTRAINT `fk__respondent_associated_respondent__idx` FOREIGN KEY (`associated_respondent_id`) REFERENCES `respondent` (`id`)
);
CREATE TABLE `respondent_condition_tag` (
  `id` varchar(41) NOT NULL
,  `respondent_id` varchar(41) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  `condition_tag_id` varchar(41) NOT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__respondent_condition_tag__condition_tag` FOREIGN KEY (`condition_tag_id`) REFERENCES `condition_tag` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__respondent_condition_tag__respondent` FOREIGN KEY (`respondent_id`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `respondent_fill` (
  `id` varchar(41) NOT NULL
,  `respondent_id` varchar(41) NOT NULL
,  `name` varchar(255) NOT NULL
,  `val` text NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `respondent_fill_respondent_id_foreign` FOREIGN KEY (`respondent_id`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `respondent_geo` (
  `id` varchar(41) NOT NULL
,  `geo_id` varchar(41) DEFAULT NULL
,  `respondent_id` varchar(41) NOT NULL
,  `previous_respondent_geo_id` varchar(41) DEFAULT NULL
,  `notes` text COLLATE BINARY
,  `is_current` integer NOT NULL DEFAULT '0'
,  `deleted_at` datetime DEFAULT NULL
,  `updated_at` datetime NOT NULL
,  `created_at` datetime NOT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `respondent_geo_geo_id_foreign` FOREIGN KEY (`geo_id`) REFERENCES `geo` (`id`)
,  CONSTRAINT `respondent_geo_previous_respondent_geo_id_foreign` FOREIGN KEY (`previous_respondent_geo_id`) REFERENCES `respondent_geo` (`id`)
,  CONSTRAINT `respondent_geo_respondent_id_foreign` FOREIGN KEY (`respondent_id`) REFERENCES `respondent` (`id`)
);
CREATE TABLE `respondent_group_tag` (
  `id` varchar(41) NOT NULL
,  `respondent_id` varchar(41) NOT NULL
,  `group_tag_id` varchar(41) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__respondent_group_tag__group_tag` FOREIGN KEY (`group_tag_id`) REFERENCES `group_tag` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__respondent_group_tag__respondent` FOREIGN KEY (`respondent_id`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `respondent_name` (
  `id` varchar(41) NOT NULL
,  `is_display_name` integer NOT NULL DEFAULT '0'
,  `name` varchar(255) NOT NULL
,  `respondent_id` varchar(41) NOT NULL
,  `locale_id` varchar(41) DEFAULT NULL
,  `previous_respondent_name_id` varchar(41) DEFAULT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `respondent_name_locale_id_foreign` FOREIGN KEY (`locale_id`) REFERENCES `locale` (`id`)
,  CONSTRAINT `respondent_name_previous_respondent_name_id_foreign` FOREIGN KEY (`previous_respondent_name_id`) REFERENCES `respondent_name` (`id`)
,  CONSTRAINT `respondent_name_respondent_id_foreign` FOREIGN KEY (`respondent_id`) REFERENCES `respondent` (`id`)
);
CREATE TABLE `respondent_photo` (
  `id` varchar(41) NOT NULL
,  `respondent_id` varchar(41) NOT NULL
,  `photo_id` varchar(41) NOT NULL
,  `sort_order` integer  NOT NULL
,  `notes` text COLLATE BINARY
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__respondent_photo__photo` FOREIGN KEY (`photo_id`) REFERENCES `photo` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__respondent_photo__respondent` FOREIGN KEY (`respondent_id`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `role` (
  `id` varchar(255) NOT NULL
,  `name` varchar(255) NOT NULL
,  `can_delete` integer NOT NULL DEFAULT '1'
,  `can_edit` integer NOT NULL DEFAULT '1'
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
);
CREATE TABLE `role_permission` (
  `id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `role_id` varchar(255) NOT NULL
,  `permission_id` varchar(255) NOT NULL
,  `value` integer NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  CONSTRAINT `role_permission_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permission` (`id`)
,  CONSTRAINT `role_permission_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`)
);
CREATE TABLE `roster` (
  `id` varchar(41) NOT NULL
,  `val` text NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
);
CREATE TABLE `section` (
  `id` varchar(41) NOT NULL
,  `name_translation_id` varchar(41) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__section_name__translation` FOREIGN KEY (`name_translation_id`) REFERENCES `translation` (`id`) ON UPDATE NO ACTION
);
CREATE TABLE `section_condition_tag` (
  `id` varchar(41) NOT NULL
,  `section_id` varchar(41) NOT NULL
,  `condition_id` varchar(41) NOT NULL
,  `survey_id` varchar(41) NOT NULL
,  `repetition` integer  NOT NULL DEFAULT '0'
,  `follow_up_datum_id` varchar(41) DEFAULT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__follow_up_datum__datum` FOREIGN KEY (`follow_up_datum_id`) REFERENCES `datum` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
,  CONSTRAINT `fk__section_condition_tag__condition_tag` FOREIGN KEY (`condition_id`) REFERENCES `condition_tag` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__section_condition_tag__section` FOREIGN KEY (`section_id`) REFERENCES `section` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__section_condition_tag__survey` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `section_question_group` (
  `id` varchar(41) NOT NULL
,  `section_id` varchar(41) NOT NULL
,  `question_group_id` varchar(41) NOT NULL
,  `question_group_order` integer  NOT NULL DEFAULT '0'
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__section_question_group__question_group` FOREIGN KEY (`question_group_id`) REFERENCES `question_group` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__section_question_group__section` FOREIGN KEY (`section_id`) REFERENCES `section` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `section_skip` (
  `id` varchar(41) NOT NULL
,  `section_id` varchar(41) NOT NULL
,  `skip_id` varchar(41) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `section_skip_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `section` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `section_skip_skip_id_foreign` FOREIGN KEY (`skip_id`) REFERENCES `skip` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `self_administered_survey` (
  `id` varchar(41) NOT NULL
,  `survey_id` varchar(41) NOT NULL
,  `login_type` text  NOT NULL DEFAULT 'id_password'
,  `url` varchar(255) NOT NULL
,  `password` varchar(255) NOT NULL
,  `hash` varchar(255) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `self_administered_survey_survey_id_foreign` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`)
);
CREATE TABLE `skip` (
  `id` varchar(41) NOT NULL
,  `show_hide` integer  NOT NULL
,  `any_all` integer  NOT NULL
,  `precedence` integer  NOT NULL DEFAULT '0'
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  `custom_logic` text COLLATE BINARY
,  PRIMARY KEY (`id`)
);
CREATE TABLE `skip_condition_tag` (
  `id` varchar(41) NOT NULL
,  `skip_id` varchar(41) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  `condition_tag_name` varchar(255) NOT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__skip_condition_tag__skip` FOREIGN KEY (`skip_id`) REFERENCES `skip` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `study` (
  `id` varchar(41) NOT NULL
,  `name` varchar(255) NOT NULL
,  `photo_quality` integer  NOT NULL DEFAULT '60'
,  `default_locale_id` varchar(41) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  `test_study_id` varchar(255) DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__study__default_locale` FOREIGN KEY (`default_locale_id`) REFERENCES `locale` (`id`) ON UPDATE NO ACTION
,  CONSTRAINT `fk__study_test_study__idx` FOREIGN KEY (`test_study_id`) REFERENCES `study` (`id`)
);
CREATE TABLE `study_form` (
  `id` varchar(41) NOT NULL
,  `study_id` varchar(41) NOT NULL
,  `form_master_id` varchar(41) NOT NULL
,  `sort_order` integer  NOT NULL DEFAULT '0'
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  `form_type_id` integer NOT NULL
,  `census_type_id` varchar(255) DEFAULT NULL
,  `geo_type_id` varchar(41) DEFAULT NULL
,  `current_version_id` varchar(255) DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__form_study_form_type_idx` FOREIGN KEY (`form_type_id`) REFERENCES `form_type` (`id`)
,  CONSTRAINT `fk__geo_type_id_geo_type__idx` FOREIGN KEY (`geo_type_id`) REFERENCES `geo_type` (`id`)
,  CONSTRAINT `fk__study_form__form` FOREIGN KEY (`form_master_id`) REFERENCES `form` (`id`) ON DELETE CASCADE
,  CONSTRAINT `fk__study_form__study` FOREIGN KEY (`study_id`) REFERENCES `study` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__study_form_census_type__idx` FOREIGN KEY (`census_type_id`) REFERENCES `census_type` (`id`)
,  CONSTRAINT `fk__study_form_current_version__idx` FOREIGN KEY (`current_version_id`) REFERENCES `form` (`id`)
);
CREATE TABLE `study_locale` (
  `id` varchar(41) NOT NULL
,  `study_id` varchar(41) NOT NULL
,  `locale_id` varchar(41) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__study_locale__locale` FOREIGN KEY (`locale_id`) REFERENCES `locale` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__study_locale__study` FOREIGN KEY (`study_id`) REFERENCES `study` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `study_parameter` (
  `id` varchar(41) NOT NULL
,  `study_id` varchar(41) NOT NULL
,  `parameter_id` varchar(41) NOT NULL
,  `val` varchar(255) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `study_parameter_parameter_id_foreign` FOREIGN KEY (`parameter_id`) REFERENCES `parameter` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `study_parameter_study_id_foreign` FOREIGN KEY (`study_id`) REFERENCES `study` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `study_respondent` (
  `id` varchar(41) NOT NULL
,  `study_id` varchar(41) NOT NULL
,  `respondent_id` varchar(41) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__study_respondent__respondent` FOREIGN KEY (`respondent_id`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__study_respondent__study` FOREIGN KEY (`study_id`) REFERENCES `study` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `survey` (
  `id` varchar(41) NOT NULL
,  `respondent_id` varchar(41) NOT NULL
,  `form_id` varchar(41) NOT NULL
,  `study_id` varchar(41) NOT NULL
,  `last_question_id` varchar(41) DEFAULT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  `completed_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__survey__form` FOREIGN KEY (`form_id`) REFERENCES `form` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__survey__last_question` FOREIGN KEY (`last_question_id`) REFERENCES `question` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
,  CONSTRAINT `fk__survey__respondent` FOREIGN KEY (`respondent_id`) REFERENCES `respondent` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__survey__study` FOREIGN KEY (`study_id`) REFERENCES `study` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `survey_condition_tag` (
  `id` varchar(41) NOT NULL
,  `survey_id` varchar(41) NOT NULL
,  `condition_id` varchar(41) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__survey_condition_tag__condition_tag` FOREIGN KEY (`condition_id`) REFERENCES `condition_tag` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__survey_condition_tag__survey` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `tag` (
  `id` varchar(41) NOT NULL
,  `name` varchar(63) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
);
CREATE TABLE `translation` (
  `id` varchar(41) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
);
CREATE TABLE `translation_text` (
  `id` varchar(41) NOT NULL
,  `translation_id` varchar(41) NOT NULL
,  `locale_id` varchar(41) DEFAULT NULL
,  `translated_text` text NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__translation_text__locale` FOREIGN KEY (`locale_id`) REFERENCES `locale` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__translation_text__translation` FOREIGN KEY (`translation_id`) REFERENCES `translation` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
CREATE TABLE `user` (
  `id` varchar(41) NOT NULL
,  `name` varchar(255) NOT NULL
,  `username` varchar(63) NOT NULL
,  `password` varchar(63) NOT NULL
,  `role` varchar(64) DEFAULT NULL
,  `selected_study_id` varchar(41) DEFAULT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  `email` varchar(255) DEFAULT NULL
,  `role_id` varchar(255) DEFAULT NULL
,  PRIMARY KEY (`id`)
,  UNIQUE (`username`)
,  UNIQUE (`username`,`deleted_at`)
,  CONSTRAINT `fk__user_role__idx` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`)
,  CONSTRAINT `fk__user_selected_study__study` FOREIGN KEY (`selected_study_id`) REFERENCES `study` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
);
CREATE TABLE `user_study` (
  `id` varchar(41) NOT NULL
,  `user_id` varchar(41) NOT NULL
,  `study_id` varchar(41) NOT NULL
,  `created_at` datetime NOT NULL
,  `updated_at` datetime NOT NULL
,  `deleted_at` datetime DEFAULT NULL
,  PRIMARY KEY (`id`)
,  CONSTRAINT `fk__user_study__study` FOREIGN KEY (`study_id`) REFERENCES `study` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
,  CONSTRAINT `fk__user_study__user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
END TRANSACTION;
