<?php

//***************************//
//* Token Controller Routes *//
//***************************//

use Illuminate\Support\Facades\Artisan;

$app->group(['namespace' => 'App\Http\Controllers', 'middleware' => 'key'], function ($app) {

    $app->post(
        'token',
        'TokenController@createToken'
    );

    //**************************//
    //* Sync v2 Controller Routes *//
    //**************************//
    $app->get(
        'device/{device_id}/syncv2/authenticate',
        'SyncControllerV2@authenticate'
    );

    $app->get(
        'device/{device_id}/syncv2/snapshot',
        'SyncControllerV2@getSnapshotInfo'
    );

    $app->get(
        'snapshot/{snapshot_id}/file_size',
        'SyncControllerV2@getSnapshotFileSize'
    );

    $app->get(
        'snapshot/{snapshot_id}/download',
        'SyncControllerV2@downloadSnapshot'
    );

    //**************************//
    //* Sync Controller Routes *//
    //**************************//

    $app->get(
        'heartbeat',
        'SyncController@heartbeat'
    );

    $app->post(
        'device/{device_id}/image',
        'SyncController@syncImages'
    );

    $app->get(
        'device/{device_id}/image',
        'SyncController@listImages'
    );

    $app->put(
        'device/{device_id}/sync',
        'SyncController@upload'
    );

    $app->post(
        'device/{device_id}/sync',
        'SyncController@download'
    );

    $app->post(
        'device/{device_id}/upload',
        'SyncController@uploadSync'
    );

    $app->get(
        'device/{device_id}/download',
        'SyncController@downloadSync'
    );


// TODO: Temporary form navigation routes
//    $app->get(
//        'form/{formId}/structure',
//        'FormController@getFormStructure'
//    );
//
//    $app->get(
//        'study/{studyId}/locales',
//        'StudyController@getLocales'
//    );
//
//    $app->get(
//        'survey/{surveyId}/actions',
//        'ActionController@getSurveyActions'
//    );
//
//    $app->get(
//        'study/{studyId}/surveys',
//        'SurveyController@getStudySurveys'
//    );
//
//    $app->get(
//        'form/action-types',
//        'ActionTypeController@getActionTypes'
//    );
//
//    $app->get(
//        'respondent/{respondentId}/study/{studyId}/forms',
//        'RespondentController@getRespondentStudyForms'
//    );
//
//    $app->get(
//        'study/{studyId}/form',
//        'FormController@getAllStudyForms'
//    );
//
//    $app->get(
//        'respondent/{respondentId}',
//        'RespondentController@getRespondentById'
//    );
//
//    $app->get(
//        'respondent',
//        'RespondentController@getAllRespondents'
//    );

    $app->group(['namespace' => 'App\Http\Controllers', 'middleware' => 'token'], function ($app) {

        //**************************//
        //* Photo Controller Routes *//
        //**************************//
//        $app->get(
//            'photo/{id}',
//            'PhotoController@getPhoto'
//        );

        //**************************//
        //* Form Controller Routes *//
        //**************************//

//        $app->get(
//            'form/{id}',
//            'FormController@getForm'
//        );

        $app->delete(
            'form/{id}',
            'FormController@removeForm'
        );

        $app->post(
            'form/{id}',
            'FormController@updateForm'
        );

        $app->post(
            'form/{form_master_id}/publish',
            'FormController@publishForm'
        );

        $app->get(
            'form',
            'FormController@getAllForms'
        );

        $app->put(
            'form',
            'FormController@createForm'
        );

        $app->patch(
            'form/reorder',
            'FormController@reorderForms'
        );

        /*
        $app->put(
            'census_form',
            'FormController@createCensusForm'
        );
        */

        $app->get(
            'study/{studyId}/form/{formId}/master/{formMasterId}/edit',
            'FormController@editFormPrep'
        );

        $app->post(
            'study/{studyId}/form/import',
            'FormController@importForm'
        );

        $app->post(
            'study/{studyId}/form/assign',
            'FormController@assignForm'
        );

        $app->post(
            'study/form/{formId}/section/import',
            'FormController@importSection'
        );

        $app->get(
            'study/{studyId}/forms',
            'RespondentController@getRespondentStudyForms'
        );


        //*******************************//
        //* Interview Controller Routes *//
        //*******************************//
        $app->get(
            'study/{id}/interview',
            'InterviewController@getInterviewPage'
        );

        $app->get(
            'study/{id}/interview/count',
            'InterviewController@getInterviewCount'
        );


        //***************************//
        //* Study Controller Routes *//
        //***************************//

        $app->get(
            'study/parameter/types',
            'QuestionParamController@getParameterTypes'
        );

        $app->delete(
            'study/{id}/parameter/{parameter_id}',
            'StudyController@deleteParameter'
        );

        $app->post(
            'study/{id}/parameter',
            'StudyController@createOrUpdateParameter'
        );

//        $app->get(
//            'study/{id}',
//            'StudyController@getStudy'
//        );

        $app->delete(
            'study/{id}',
            'StudyController@removeStudy'
        );

        $app->post(
            'study/{id}',
            'StudyController@updateStudy'
        );

        $app->get(
            'study',
            'StudyController@getAllStudies'
        );

        $app->put(
            'study',
            'StudyController@createStudy'
        );

        $app->put(
            'study/{study_id}/locales/{locale_id}',
            'StudyController@saveLocale'
        );

        $app->delete(
            'study/{study_id}/locales/{locale_id}',
            'StudyController@deleteLocale'
        );

        //**************************//
        //* User Controller Routes *//
        //**************************//

        $app->get(
            'user/me',
            'UserController@getMe'
        );

        $app->get(
            'user/{id}',
            'UserController@getUser'
        );

        $app->delete(
            'user/{id}',
            'UserController@removeUser'
        );

        $app->get(
            'user',
            'UserController@getAllUsers'
        );

        $app->put(
            'user/{user_id}/studies/{study_id}',
            'UserController@saveStudy'
        );

        $app->delete(
            'user/{user_id}/studies/{study_id}',
            'UserController@deleteStudy'
        );

        //****************************//
        //* Locale Controller Routes *//
        //****************************//

        $app->get(
            'locale/{id}',
            'LocaleController@getLocale'
        );

        $app->delete(
            'locale/{id}',
            'LocaleController@removeLocale'
        );

        $app->post(
            'locale/{id}',
            'LocaleController@updateLocale'
        );

        $app->get(
            'locale',
            'LocaleController@getAllLocales'
        );

        $app->put(
            'locale',
            'LocaleController@createLocale'
        );

        //************************************//
        //* Group Tag Type Controller Routes *//
        //************************************//

        $app->delete(
            'group_tag_type/{id}',
            'GroupTagTypeController@removeGroupTagType'
        );

        $app->get(
            'group_tag_type',
            'GroupTagTypeController@getAllGroupTagTypes'
        );

        $app->put(
            'group_tag_type',
            'GroupTagTypeController@createGroupTagType'
        );

        //****************************//
        //* Device Controller Routes *//
        //****************************//

        $app->get(
            'device/{id}',
            'DeviceController@getDevice'
        );

        $app->delete(
            'device/{id}',
            'DeviceController@removeDevice'
        );

        $app->post(
            'device/{id}',
            'DeviceController@updateDevice'
        );

        $app->get(
            'device',
            'DeviceController@getAllDevices'
        );

        $app->put(
            'device',
            'DeviceController@createDevice'
        );

        //****************************//
        //* Respondent Controller Routes *//
        //****************************//

        $app->post(
            'study/{studyId}/respondent/import',
            'RespondentController@importRespondents'
        );

        $app->post(
            'study/{studyId}/respondent-photo/import',
            'RespondentController@importRespondentPhotos'
        );

//        $app->get(
//            'study/{study_id}/respondents',
//            'RespondentController@getAllRespondentsByStudyId'
//        );

        $app->get(
            'respondent/{study_id}/count',
            'RespondentController@getRespondentCountByStudyId'
        );

        $app->get(
            'respondent/{study_id}/search',
            'RespondentController@searchRespondentsByStudyId'
        );

        $app->put(
            'respondent',
            'RespondentController@createRespondent'
        );

        $app->delete(
            'respondent/{id}',
            'RespondentController@removeRespondent'
        );

        $app->post(
            'respondent/{id}',
            'RespondentController@updateRespondent'
        );

        $app->post(
            'respondent/{respondent_id}/photos',
            'RespondentController@addPhoto'
        );

        $app->delete(
            'respondent/{respondent_id}/photo/{photo_id}',
            'RespondentController@removeRespondentPhoto'
        );

        //**************************************//
        //* Translation Controller Routes *//
        //**************************************//

        $app->get(
            'translation/{translation_id}/text/{text_id}',
            'TranslationTextController@getTranslationText'
        );

        $app->delete(
            'translation/{translation_id}',
            'TranslationController@removeTranslation'
        );

        $app->delete(
            'translation/{translation_id}/text/{text_id}',
            'TranslationTextController@removeTranslationText'
        );

        $app->post(
            'translation/{translation_id}/text/{text_id}',
            'TranslationTextController@updateTranslationText'
        );

        $app->get(
            'translation/{translation_id}/text',
            'TranslationTextController@getAllTranslationText'
        );

        $app->put(
            'translation',
            'TranslationController@createTranslation'
        );

        $app->put(
            'translation/{translation_id}/text',
            'TranslationTextController@createTranslationText'
        );

        //************************************//
        //* Question Group Controller Routes *//
        //************************************//

        $app->get(
            'form/section/group/{group_id}/question/',
            'QuestionGroupController@getQuestionGroup'
        );

        $app->delete(
            'form/section/group/{group_id}',
            'QuestionGroupController@removeQuestionGroup'
        );

        $app->get(
            'form/{form_id}/section/group/locale/{locale_id}',
            'QuestionGroupController@getAllQuestionGroups'
        );

        $app->put(
            'form/section/{section_id}/group/question',
            'QuestionGroupController@createQuestionGroup'
        );

        $app->post(
            'form/section/group/{group_id}/question/',
            'QuestionGroupController@updateQuestionGroup'
        );

        // Route to update / reorder multiple section questions groups at once
        $app->patch(
            'form/section/groups',
            'QuestionGroupController@updateSectionQuestionGroups'
        );

        //*****************************//
        //* Section Controller Routes *//
        //*****************************//

        $app->get(
            'form/section/{section_id}',
            'SectionController@getSection'
        );

        $app->delete(
            'form/section/{section_id}',
            'SectionController@removeSection'
        );

        $app->post(
            'form/section/{section_id}',
            'SectionController@updateSection'
        );

        $app->get(
            'form/{form_id}/section/locale/{locale_id}',
            'SectionController@getAllSections'
        );

        $app->put(
            'form/{form_id}/section',
            'SectionController@createSection'
        );

        // Route to update / reorder multiple form_section rows at once
        $app->patch(
            'form/sections',
            'SectionController@updateSections'
        );

        //**********************************//
        //* Form Section Controller Routes *//
        //**********************************//

        $app->post(
            'form_section/{form_section_id}',
            'FormSectionController@updateFormSection'
        );


        //****************************************//
        //* Question Condition Controller Routes *//
        //****************************************//

        $app->put(
            'form/section/group/question/condition/logic',
            'ConditionController@editConditionLogic'
        );

        $app->put(
            'form/section/group/question/condition/scope',
            'ConditionController@editConditionScope'
        );

        $app->put(
            'form/section/group/question/condition/tag',
            'ConditionController@createCondition'
        );

        $app->get(
            'form/section/group/question/condition/tag',
            'ConditionController@getAllConditions'
        );

        $app->get(
            'form/section/group/question/condition/tag/unique',
            'ConditionController@getAllUniqueConditions'
        );

        $app->post(
            'form/section/group/question/condition/tag/search',
            'ConditionController@searchAllConditions'
        );

        $app->put(
            'question/{question_id}/assign_condition_tag',
            'QuestionController@createAssignConditionTag'
        );

        $app->post(
            'question/{question_id}/assign_condition_tag',
            'QuestionController@updateAssignConditionTag'
        );

        $app->delete(
            'form/section/group/question/condition/{id}',
            'ConditionController@deleteAssignConditionTag'
        );

        //**************************//
        //* Skip Controller Routes *//
        //**************************//

        $app->put(
            'form/skip/',
            'SkipController@createSkipGeneralized'
        );

        $app->put(
            'form/section/group/skip/',
            'SkipController@createQuestionGroupSkip'
        );

        $app->post(
            'form/section/group/skip/{id}',
            'SkipController@updateQuestionGroupSkip'
        );

        $app->delete(
            'form/section/group/skip/{id}',
            'SkipController@deleteQuestionGroupSkip'
        );

        $app->get(
            'form/section/group/skip/',
            'SkipController@getAllQuestionGroupSkips'
        );

        //******************************//
        //* Question Controller Routes *//
        //******************************//

        $app->put(
            'form/section/group/{group_id}/question/',
            'QuestionController@createQuestion'
        );

        $app->post(
            'form/section/group/{group_id}/question/{question_id}',
            'QuestionController@moveQuestion'
        );

        $app->delete(
            'form/section/group/question/{question_id}',
            'QuestionController@removeQuestion'
        );


        $app->get(
            'form/section/group/question/{question_id}',
            'QuestionController@getQuestion'
        );

        $app->get(
            'form/{form_id}/section/group/question/locale/{locale_id}',
            'QuestionController@getAllQuestions'
        );

        $app->post(
            'form/section/group/question/{question_id}',
            'QuestionController@updateQuestion'
        );

        // Route to update / reorder multiple questions at once
        $app->patch(
            'form/section/group/questions',
            'QuestionController@updateQuestions'
        );

        // Route to update / reorder multiple question_choice rows at once
        $app->patch(
            'form/section/group/question/choices',
            'QuestionController@updateChoices'
        );

        //************************************//
        //* Question Type Controller Routers *//
        //************************************//

        $app->put(
            'question/type',
            'QuestionTypeController@createQuestionType'
        );

        $app->delete(
            'question/type/{question_type_id}',
            'QuestionTypeController@removeQuestionType'
        );

        $app->get(
            'question/type/{question_type_id}',
            'QuestionTypeController@getQuestionType'
        );

        $app->get(
            'question/type',
            'QuestionTypeController@getAllQuestionTypes'
        );

        $app->post(
            'question/type/{question_type_id}',
            'QuestionTypeController@updateQuestionType'
        );

        //*************************************//
        //* Question Choice Controller Routes *//
        //*************************************//

        $app->put(
            'form/section/group/question/{question_id}/choice',
            'QuestionChoiceController@createNewQuestionChoice'
        );

        $app->delete(
            'form/section/group/question/choice/{question_choice_id}',
            'QuestionChoiceController@removeQuestionChoice'
        );

        $app->delete(
            'form/section/group/question/{question_id}/choice/{choice_id}',
            'QuestionChoiceController@removeChoice'
        );

        $app->get(
            'form/section/group/question/choice/{choice_id}',
            'QuestionChoiceController@getQuestionChoice'
        );

        $app->get(
            'form/{form_id}/section/group/question/choice/locale/{locale_id}',
            'QuestionChoiceController@getAllQuestionChoices'
        );

        $app->post(
            'form/section/group/question/choice/{choice_id}',
            'QuestionChoiceController@updateQuestionChoice'
        );

        $app->post(
            'form/section/group/question/{question_id}/choices',
            'QuestionChoiceController@updateQuestionChoices'
        );

        //*************************//
        //* Geo Controller Routes *//
        //*************************//

        $app->put(
            'geo/id/locale/{locale_id}',
            'GeoController@createGeo'
        );

        $app->delete(
            'geo/id/{geo_id}',
            'GeoController@removeGeo'
        );

        $app->get(
            'geo/id/locale/{locale_id}',
            'GeoController@getAllGeos'
        );

        $app->get(
            'study/{study_id}/geo',
            'GeoController@getAllGeosByStudyId'
        );

        $app->get(
            'study/{study_id}/geo/count',
            'GeoController@getGeoCountByStudyId'
        );

        $app->get(
            'geo/id/{geo_id}',
            'GeoController@getGeo'
        );

        $app->post(
            'geo/id/{geo_id}',
            'GeoController@updateGeo'
        );

        //******************************//
        //* Geo Type Controller Routes *//
        //******************************//

        $app->put(
            'geo/type',
            'GeoTypeController@createGeoType'
        );

        $app->delete(
            'geo/type/{geo_type_id}',
            'GeoTypeController@removeGeoType'
        );

        $app->get(
            'geo/type/{geo_type_id}',
            'GeoTypeController@getGeoType'
        );

        $app->get(
            'geo/type',
            'GeoTypeController@getAllGeoTypes'
        );

        $app->get(
            'study/{study_id}/geo/type',
            'GeoTypeController@getAllGeoTypesByStudyId'
        );


        $app->get(
            'geo/type/{parent_geo_id}/parent',
            'GeoTypeController@getAllEligibleGeoTypesOfParentGeo'
        );

        $app->post(
            'geo/type/{geo_type_id}',
            'GeoTypeController@updateGeoType'
        );

        //************************************//
        //* Question Param Controller Routes *//
        //************************************//

        $app->post(
            'form/section/group/question/{question_id}/type/numeric',
            'QuestionParamController@updateQuestionNumeric'
        );

        $app->post(
            'form/section/group/question/{question_id}/type/multiple',
            'QuestionController@updateQuestionTypeMultiple'
        );

        $app->post(
            'form/section/group/question/{question_id}/type/datetime',
            'QuestionParamController@updateQuestionDateTime'
        );


        // Web app
        $app->get(
            'form/parameter/types',
            'QuestionParamController@getParameterTypes'
        );

        $app->post(
            'form/section/group/question/{question_id}/parameter',
            'QuestionParamController@createOrUpdateParameter'
        );

        $app->delete(
            'parameter/{parameter_id}',
            'QuestionParamController@deleteQuestionParameter'
        );


    });


    $app->group(['namespace' => 'App\Http\Controllers', 'middleware' => ['token', 'role:whitelist,ADMIN']], function ($app) {

        //**********************//
        //* Create User Route *//
        //**********************//
        $app->put(
            'user',
            'UserController@createUser'
        );

        $app->post(
            'user/{id}',
            'UserController@updateUser'
        );

        //**********************//
        //* Report Routes *//
        //**********************//
        $app->get(
            'report/completed',
            'ReportController@getAllSavedReports'
        );

        $app->get(
            'report/download/{file_name}',
            'ReportController@downloadFile'
        );

        $app->post(
            'report/form/{form_id}',
            'ReportController@dispatchFormReport'
        );

        $app->post(
            'report/study/{study_id}/respondents',
            'ReportController@dispatchRespondentReport'
        );

        $app->post(
            'report/study/{study_id}/edges',
            'ReportController@dispatchEdgesReport'
        );

        $app->post(
            'report/study/{study_id}/geo',
            'ReportController@dispatchGeoReport'
        );

        $app->post(
            'report/study/{study_id}/interview',
            'ReportController@dispatchInterviewReport'
        );

        $app->post(
            'report/study/{study_id}/timing',
            'ReportController@dispatchTimingReport'
        );

        $app->get(
            'report/{report_id}',
            'ReportController@getReport'
        );

        $app->get(
            'report/{report_id}/status',
            'ReportController@getReportStatus'
        );

        $app->post(
            'report/images',
            'PhotoController@getZipPhotos'
        );

        $app->post(
            'report/clean',
            'ReportController@cleanReports'
        );
//
//    $app->get(
//        'report/generate',
//        function(){
//            Artisan::call("trellis:make:reports");
//        }
//    );
//
//    $app->get(
//        'report/bundle',
//        function(){
//            Artisan::call("trellis:bundle:reports");
//        }
//    );

    });
});