<?php

//***************************//
//* Token Controller Routes *//
//***************************//

$router->group(['middleware' => 'key'], function () use ($router) {

    $router->post(
        'token',
        'TokenController@createToken'
    );


    //**************************//
    //* Sync Controller Routes *//
    //**************************//

    $router->get(
        'heartbeat',
        'SyncController@heartbeat'
    );

    $router->post(
        'device/{device_id}/image',
        'SyncController@syncImages'
    );

    $router->get(
        'device/{device_id}/image',
        'SyncController@listImages'
    );

    $router->put(
        'device/{device_id}/sync',
        'SyncController@upload'
    );

    $router->post(
        'device/{device_id}/sync',
        'SyncController@download'
    );

    $router->post(
        'device/{device_id}/upload',
        'SyncController@uploadSync'
    );

    $router->get(
        'device/{device_id}/download',
        'SyncController@downloadSync'
    );


// TODO: Temporary form navigation routes
//    $router->get(
//        'form/{formId}/structure',
//        'FormController@getFormStructure'
//    );
//
//    $router->get(
//        'study/{studyId}/locales',
//        'StudyController@getLocales'
//    );
//
//    $router->get(
//        'survey/{surveyId}/actions',
//        'ActionController@getSurveyActions'
//    );
//
//    $router->get(
//        'study/{studyId}/surveys',
//        'SurveyController@getStudySurveys'
//    );
//
//    $router->get(
//        'form/action-types',
//        'ActionTypeController@getActionTypes'
//    );
//
//    $router->get(
//        'respondent/{respondentId}/study/{studyId}/forms',
//        'RespondentController@getRespondentStudyForms'
//    );
//
    $router->get(
        'study/{studyId}/form',
        'FormController@getAllStudyForms'
    );
//
//    $router->get(
//        'respondent/{respondentId}',
//        'RespondentController@getRespondentById'
//    );
//
//    $router->get(
//        'respondent',
//        'RespondentController@getAllRespondents'
//    );

    $router->group(['middleware' => 'token'], function () use ($router) {

        //**************************//
        //* Photo Controller Routes *//
        //**************************//
        $router->get(
            'photo/{id}',
            'PhotoController@getPhoto'
        );

        //*********************************//
        //* Census Form Controller Routes *//
        //*********************************//
        $router->get('form/census/types',   'CensusFormController@getCensusFormTypes');

        //**************************//
        //* Form Controller Routes *//
        //**************************//

        $router->put('study/{study_id}/form/{form_id}',    'FormController@updateStudyForm');

        $router->get(
            'form/{id}',
            'FormController@getForm'
        );

        $router->delete(
            'form/{id}',
            'FormController@removeForm'
        );

        $router->post(
            'form/{id}',
            'FormController@updateForm'
        );

        $router->post(
            'form/{form_master_id}/publish',
            'FormController@publishForm'
        );

        $router->get(
            'form',
            'FormController@getAllForms'
        );

        $router->put(
            'form',
            'FormController@createForm'
        );

        $router->patch(
            'form/reorder',
            'FormController@reorderForms'
        );

        /*
        $router->put(
            'census_form',
            'FormController@createCensusForm'
        );
        */

        $router->get(
            'study/{studyId}/form/{formId}/master/{formMasterId}/edit',
            'FormController@editFormPrep'
        );

        $router->post(
            'study/{studyId}/form/import',
            'FormController@importForm'
        );

        $router->post(
            'study/{studyId}/form/assign',
            'FormController@assignForm'
        );

        $router->post(
            'study/form/{formId}/section/import',
            'FormController@importSection'
        );

        $router->get(
            'study/{studyId}/forms',
            'RespondentController@getRespondentStudyForms'
        );


        //*******************************//
        //* Interview Controller Routes *//
        //*******************************//
        $router->get(
            'study/{id}/interview',
            'InterviewController@getInterviewPage'
        );

        $router->get(
            'study/{id}/interview/count',
            'InterviewController@getInterviewCount'
        );


        //***************************//
        //* Study Controller Routes *//
        //***************************//

        $router->get(
            'study/parameter/types',
            'QuestionParamController@getParameterTypes'
        );

        $router->delete(
            'study/{id}/parameter/{parameter_id}',
            'StudyController@deleteParameter'
        );

        $router->post(
            'study/{id}/parameter',
            'StudyController@createOrUpdateParameter'
        );

//        $router->get(
//            'study/{id}',
//            'StudyController@getStudy'
//        );

        $router->delete(
            'study/{id}',
            'StudyController@removeStudy'
        );

        $router->post(
            'study/{id}',
            'StudyController@updateStudy'
        );

        $router->get('study/{id}', 'StudyController@getStudy');

        $router->get(
            'study',
            'StudyController@getAllStudies'
        );

        $router->put(
            'study',
            'StudyController@createStudy'
        );

        $router->put(
            'study/{study_id}/locales/{locale_id}',
            'StudyController@saveLocale'
        );

        $router->delete(
            'study/{study_id}/locales/{locale_id}',
            'StudyController@deleteLocale'
        );

        //**************************//
        //* User Controller Routes *//
        //**************************//

        $router->get(
            'user/me',
            'UserController@getMe'
        );

        $router->get(
            'user/{id}',
            'UserController@getUser'
        );

        $router->delete(
            'user/{id}',
            'UserController@removeUser'
        );

        $router->get(
            'user',
            'UserController@getAllUsers'
        );


        $router->put(
            'user/{user_id}/studies/{study_id}',
            'UserController@saveStudy'
        );

        $router->delete(
            'user/{user_id}/studies/{study_id}',
            'UserController@deleteStudy'
        );

        //****************************//
        //* Locale Controller Routes *//
        //****************************//

        $router->get(
            'locale/{id}',
            'LocaleController@getLocale'
        );

        $router->delete(
            'locale/{id}',
            'LocaleController@removeLocale'
        );

        $router->post(
            'locale/{id}',
            'LocaleController@updateLocale'
        );

        $router->get(
            'locale',
            'LocaleController@getAllLocales'
        );

        $router->put(
            'locale',
            'LocaleController@createLocale'
        );

        //************************************//
        //* Group Tag Type Controller Routes *//
        //************************************//

        $router->delete(
            'group_tag_type/{id}',
            'GroupTagTypeController@removeGroupTagType'
        );

        $router->get(
            'group_tag_type',
            'GroupTagTypeController@getAllGroupTagTypes'
        );

        $router->put(
            'group_tag_type',
            'GroupTagTypeController@createGroupTagType'
        );

        //****************************//
        //* Device Controller Routes *//
        //****************************//

        $router->get(
            'device/{id}',
            'DeviceController@getDevice'
        );

        $router->delete(
            'device/{id}',
            'DeviceController@removeDevice'
        );

        $router->post(
            'device/{id}',
            'DeviceController@updateDevice'
        );

        $router->get(
            'device',
            'DeviceController@getAllDevices'
        );

        $router->put(
            'device',
            'DeviceController@createDevice'
        );

        //****************************//
        //* Respondent Controller Routes *//
        //****************************//

        $router->post(
            'study/{studyId}/respondent/import',
            'RespondentController@importRespondents'
        );

        $router->post(
            'study/{studyId}/respondent-photo/import',
            'RespondentController@importRespondentPhotos'
        );

        $router->post(
            'respondent-preload-data/import',
            'RespondentController@preloadRespondentData'
        );

//        $router->get(
//            'study/{study_id}/respondents',
//            'RespondentController@getAllRespondentsByStudyId'
//        );

        $router->get(
            'respondent/{study_id}/count',
            'RespondentController@getRespondentCountByStudyId'
        );

        $router->get(
            'respondent/{study_id}/search',
            'RespondentController@searchRespondentsByStudyId'
        );

        $router->put(
            'respondent',
            'RespondentController@createRespondent'
        );

        $router->delete(
            'respondent/{id}',
            'RespondentController@removeRespondent'
        );

        $router->post(
            'respondent/{id}',
            'RespondentController@updateRespondent'
        );

        $router->post(
            'respondent/{respondent_id}/photos',
            'RespondentController@addPhoto'
        );

        $router->delete(
            'respondent/{respondent_id}/photo/{photo_id}',
            'RespondentController@removeRespondentPhoto'
        );

        //**************************************//
        //* Translation Controller Routes *//
        //**************************************//

        $router->get(
            'translation/{translation_id}/text/{text_id}',
            'TranslationTextController@getTranslationText'
        );

        $router->delete(
            'translation/{translation_id}',
            'TranslationController@removeTranslation'
        );

        $router->delete(
            'translation/{translation_id}/text/{text_id}',
            'TranslationTextController@removeTranslationText'
        );

        $router->post(
            'translation/{translation_id}/text/{text_id}',
            'TranslationTextController@updateTranslationText'
        );

        $router->get(
            'translation/{translation_id}/text',
            'TranslationTextController@getAllTranslationText'
        );

        $router->put(
            'translation',
            'TranslationController@createTranslation'
        );

        $router->put(
            'translation/{translation_id}/text',
            'TranslationTextController@createTranslationText'
        );

        //************************************//
        //* Question Group Controller Routes *//
        //************************************//

        $router->get(
            'form/section/group/{group_id}/question/',
            'QuestionGroupController@getQuestionGroup'
        );

        $router->delete(
            'form/section/group/{group_id}',
            'QuestionGroupController@removeQuestionGroup'
        );

        $router->get(
            'form/{form_id}/section/group/locale/{locale_id}',
            'QuestionGroupController@getAllQuestionGroups'
        );

        $router->put(
            'form/section/{section_id}/group/question',
            'QuestionGroupController@createQuestionGroup'
        );

        $router->post(
            'form/section/group/{group_id}/question/',
            'QuestionGroupController@updateQuestionGroup'
        );

        // Route to update / reorder multiple section questions groups at once
        $router->patch(
            'form/section/groups',
            'QuestionGroupController@updateSectionQuestionGroups'
        );

        //*****************************//
        //* Section Controller Routes *//
        //*****************************//

        $router->get(
            'form/section/{section_id}',
            'SectionController@getSection'
        );

        $router->delete(
            'form/section/{section_id}',
            'SectionController@removeSection'
        );

        $router->post(
            'form/section/{section_id}',
            'SectionController@updateSection'
        );

        $router->get(
            'form/{form_id}/section/locale/{locale_id}',
            'SectionController@getAllSections'
        );

        $router->put(
            'form/{form_id}/section',
            'SectionController@createSection'
        );

        // Route to update / reorder multiple form_section rows at once
        $router->patch(
            'form/sections',
            'SectionController@updateSections'
        );

        //**********************************//
        //* Form Section Controller Routes *//
        //**********************************//

        $router->post(
            'form_section/{form_section_id}',
            'FormSectionController@updateFormSection'
        );


        //****************************************//
        //* Question Condition Controller Routes *//
        //****************************************//

        $router->put(
            'form/section/group/question/condition/logic',
            'ConditionController@editConditionLogic'
        );

        $router->put(
            'form/section/group/question/condition/scope',
            'ConditionController@editConditionScope'
        );

        $router->put(
            'form/section/group/question/condition/tag',
            'ConditionController@createCondition'
        );

        $router->get(
            'form/section/group/question/condition/tag',
            'ConditionController@getAllConditions'
        );

        $router->get(
            'form/section/group/question/condition/tag/unique',
            'ConditionController@getAllUniqueConditions'
        );

        $router->post(
            'form/section/group/question/condition/tag/search',
            'ConditionController@searchAllConditions'
        );

        $router->put(
            'question/{question_id}/assign_condition_tag',
            'QuestionController@createAssignConditionTag'
        );

        $router->post(
            'question/{question_id}/assign_condition_tag',
            'QuestionController@updateAssignConditionTag'
        );

        $router->delete(
            'form/section/group/question/condition/{id}',
            'ConditionController@deleteAssignConditionTag'
        );

        //**************************//
        //* Skip Controller Routes *//
        //**************************//

        $router->put(
            'form/skip/',
            'SkipController@createSkipGeneralized'
        );

        $router->put(
            'form/section/group/skip/',
            'SkipController@createQuestionGroupSkip'
        );

        $router->post(
            'form/section/group/skip/{id}',
            'SkipController@updateQuestionGroupSkip'
        );

        $router->delete(
            'form/section/group/skip/{id}',
            'SkipController@deleteQuestionGroupSkip'
        );

        $router->get(
            'form/section/group/skip/',
            'SkipController@getAllQuestionGroupSkips'
        );

        //******************************//
        //* Question Controller Routes *//
        //******************************//

        $router->put(
            'form/section/group/{group_id}/question/',
            'QuestionController@createQuestion'
        );

        $router->post(
            'form/section/group/{group_id}/question/{question_id}',
            'QuestionController@moveQuestion'
        );

        $router->delete(
            'form/section/group/question/{question_id}',
            'QuestionController@removeQuestion'
        );


        $router->get(
            'form/section/group/question/{question_id}',
            'QuestionController@getQuestion'
        );

        $router->get(
            'form/{form_id}/section/group/question/locale/{locale_id}',
            'QuestionController@getAllQuestions'
        );

        $router->post(
            'form/section/group/question/{question_id}',
            'QuestionController@updateQuestion'
        );

        // Route to update / reorder multiple questions at once
        $router->patch(
            'form/section/group/questions',
            'QuestionController@updateQuestions'
        );

        // Route to update / reorder multiple question_choice rows at once
        $router->patch(
            'form/section/group/question/choices',
            'QuestionController@updateChoices'
        );

        //************************************//
        //* Question Type Controller Routers *//
        //************************************//

        $router->put(
            'question/type',
            'QuestionTypeController@createQuestionType'
        );

        $router->delete(
            'question/type/{question_type_id}',
            'QuestionTypeController@removeQuestionType'
        );

        $router->get(
            'question/type/{question_type_id}',
            'QuestionTypeController@getQuestionType'
        );

        $router->get(
            'question/type',
            'QuestionTypeController@getAllQuestionTypes'
        );

        $router->post(
            'question/type/{question_type_id}',
            'QuestionTypeController@updateQuestionType'
        );

        //*************************************//
        //* Question Choice Controller Routes *//
        //*************************************//

        $router->put(
            'form/section/group/question/{question_id}/choice',
            'QuestionChoiceController@createNewQuestionChoice'
        );

        $router->delete(
            'form/section/group/question/choice/{question_choice_id}',
            'QuestionChoiceController@removeQuestionChoice'
        );

        $router->delete(
            'form/section/group/question/{question_id}/choice/{choice_id}',
            'QuestionChoiceController@removeChoice'
        );

        $router->get(
            'form/section/group/question/choice/{choice_id}',
            'QuestionChoiceController@getQuestionChoice'
        );

        $router->get(
            'form/{form_id}/section/group/question/choice/locale/{locale_id}',
            'QuestionChoiceController@getAllQuestionChoices'
        );

        $router->post(
            'form/section/group/question/choice/{choice_id}',
            'QuestionChoiceController@updateQuestionChoice'
        );

        $router->post(
            'form/section/group/question/{question_id}/choices',
            'QuestionChoiceController@updateQuestionChoices'
        );

        //*************************//
        //* Geo Controller Routes *//
        //*************************//

        $router->put(
            'geo/id/locale/{locale_id}',
            'GeoController@createGeo'
        );

        $router->delete(
            'geo/id/{geo_id}',
            'GeoController@removeGeo'
        );

        $router->get(
            'geo/id/locale/{locale_id}',
            'GeoController@getAllGeos'
        );

        $router->get(
            'study/{study_id}/geo',
            'GeoController@getAllGeosByStudyId'
        );

        $router->get(
            'study/{study_id}/geo/count',
            'GeoController@getGeoCountByStudyId'
        );

        $router->get(
            'geo/id/{geo_id}',
            'GeoController@getGeo'
        );

        $router->post(
            'geo/id/{geo_id}',
            'GeoController@updateGeo'
        );

        //******************************//
        //* Geo Type Controller Routes *//
        //******************************//

        $router->put(
            'geo/type',
            'GeoTypeController@createGeoType'
        );

        $router->delete(
            'geo/type/{geo_type_id}',
            'GeoTypeController@removeGeoType'
        );

        $router->get(
            'geo/type/{geo_type_id}',
            'GeoTypeController@getGeoType'
        );

        $router->get(
            'geo/type',
            'GeoTypeController@getAllGeoTypes'
        );

        $router->get(
            'study/{study_id}/geo/type',
            'GeoTypeController@getAllGeoTypesByStudyId'
        );

        $router->get(
            'geo/type/{parent_geo_id}/parent',
            'GeoTypeController@getAllEligibleGeoTypesOfParentGeo'
        );

        $router->post(
            'geo/type/{geo_type_id}',
            'GeoTypeController@updateGeoType'
        );

        //************************************//
        //* Question Param Controller Routes *//
        //************************************//

        $router->post(
            'form/section/group/question/{question_id}/type/numeric',
            'QuestionParamController@updateQuestionNumeric'
        );

        $router->post(
            'form/section/group/question/{question_id}/type/multiple',
            'QuestionController@updateQuestionTypeMultiple'
        );

        $router->post(
            'form/section/group/question/{question_id}/type/datetime',
            'QuestionParamController@updateQuestionDateTime'
        );


        // Web app
        $router->get(
            'form/parameter/types',
            'QuestionParamController@getParameterTypes'
        );

        $router->post(
            'form/section/group/question/{question_id}/parameter',
            'QuestionParamController@createOrUpdateParameter'
        );

        $router->delete(
            'parameter/{parameter_id}',
            'QuestionParamController@deleteQuestionParameter'
        );


    });


    $router->group(['middleware' => ['token', 'role:whitelist,ADMIN']], function () use ($router) {

        //**********************//
        //* Create User Route *//
        //**********************//
        $router->put(
            'user',
            'UserController@createUser'
        );

        $router->post(
            'user/{id}',
            'UserController@updateUser'
        );

        //**********************//
        //* Report Routes *//
        //**********************//
        $router->get(
            'report/completed',
            'ReportController@getAllSavedReports'
        );

        $router->get(
            'report/download/{file_name}',
            'ReportController@downloadFile'
        );

        $router->post(
            'report/form/{form_id}',
            'ReportController@dispatchFormReport'
        );

        $router->post(
            'report/study/{study_id}/respondents',
            'ReportController@dispatchRespondentReport'
        );

        $router->post(
            'report/study/{study_id}/edges',
            'ReportController@dispatchEdgesReport'
        );

        $router->post(
            'report/study/{study_id}/geo',
            'ReportController@dispatchGeoReport'
        );

        $router->post(
            'report/study/{study_id}/interview',
            'ReportController@dispatchInterviewReport'
        );

        $router->post(
            'report/study/{study_id}/timing',
            'ReportController@dispatchTimingReport'
        );

        $router->get(
            'report/{report_id}',
            'ReportController@getReport'
        );

        $router->get(
            'report/{report_id}/status',
            'ReportController@getReportStatus'
        );

        $router->post(
            'report/images',
            'PhotoController@getZipPhotos'
        );

        $router->post(
            'report/clean',
            'ReportController@cleanReports'
        );
//
//    $router->get(
//        'report/generate',
//        function(){
//            Artisan::call("trellis:make:reports");
//        }
//    );
//
//    $router->get(
//        'report/bundle',
//        function(){
//            Artisan::call("trellis:bundle:reports");
//        }
//    );

    });
});