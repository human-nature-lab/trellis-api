<?php

$router->post('token',                                                                       'TokenController@createToken');     // Login
$router->get('config',                                                                       'ConfigController@all');            // View server configuration

$router->group(['middleware' => 'token'], function () use ($router) {

  $router->get('roles',                                     ['middleware' => 'requires:VIEW_PERMISSIONS',               'uses' => 'RoleController@all']);
  $router->post('role',                                     ['middleware' => 'requires:EDIT_PERMISSIONS',               'uses' => 'RoleController@create']);
  $router->put('role/copy',                                 ['middleware' => 'requires:EDIT_PERMISSIONS',               'uses' => 'RoleController@copy']);
  $router->delete('role/{role_id}',                         ['middleware' => 'requires:EDIT_PERMISSIONS',               'uses' => 'RoleController@remove']);
  $router->put('role/{role_id}/permission/{permission_id}', ['middleware' => 'requires:EDIT_PERMISSIONS',               'uses' => 'PermissionController@updateRolePermission']);
  $router->get('permissions',                               ['middleware' => 'requires:VIEW_PERMISSIONS',               'uses' => 'PermissionController@all']);

  //* Photo Controller Routes *//
  $router->get('photo/{id}',                                [                                                           'uses' => 'PhotoController@getPhoto']);

  //* Census Form Controller Routes *//
  $router->get('form/census/types',   'CensusFormController@getCensusFormTypes');

  //* Form Controller Routes *//
  $router->get('study/{studyId}/form',                                        [                                         'uses' => 'FormController@getAllStudyForms']);
  $router->get('form/{id}',                                                   [                                         'uses' => 'FormController@getForm']);
  $router->get('form',                                                        [                                         'uses' => 'FormController@getAllForms']);
  $router->get('study/{studyId}/forms',                                       [                                         'uses' => 'RespondentController@getRespondentStudyForms']);
  $router->post('study/{study_id}/form',                                      ['middleware' => 'requires:ADD_FORM',     'uses' => 'FormController@createForm']);
  $router->post('study/{studyId}/form/import',                                ['middleware' => 'requires:ADD_FORM',     'uses' => 'FormController@importForm']);
  $router->delete('study/{study_id}/form/{form_id}',                          ['middleware' => 'requires:REMOVE_FORM',  'uses' => 'FormController@removeForm']);
  $router->put('study/{study_id}/form/{form_id}',                             ['middleware' => 'requires:EDIT_FORM',    'uses' => 'FormController@updateStudyForm']);
  $router->put('form/{id}',                                                   ['middleware' => 'requires:EDIT_FORM',    'uses' => 'FormController@updateForm']);
  $router->put('form/{form_master_id}/publish',                               ['middleware' => 'requires:EDIT_FORM',    'uses' => 'FormController@publishForm']);
  $router->patch('study/{studyId}/forms/reorder',                              ['middleware' => 'requires:EDIT_FORM',    'uses' => 'FormController@reorderForms']);
  $router->get('study/{studyId}/form/{formId}/master/{formMasterId}/edit',    ['middleware' => 'requires:EDIT_FORM',    'uses' => 'FormController@editFormPrep']);
  $router->post('study/{studyId}/form/assign',                                ['middleware' => 'requires:EDIT_FORM',    'uses' => 'FormController@assignForm']);
  $router->post('study/form/{formId}/section/import',                         ['middleware' => 'requires:EDIT_FORM',    'uses' => 'FormController@importSection']);

  //* Study Controller Routes *//
  $router->get('study/parameter/types',                           [                                                     'uses' => 'QuestionParamController@getParameterTypes']);
  $router->delete('study/{id}/parameter/{parameter_id}',          [                                                     'uses' => 'StudyController@deleteParameter']);
  $router->post('study/{id}/parameter',                           [                                                     'uses' => 'StudyController@createOrUpdateParameter']);
  $router->get('study/{id}',                                      [                                                     'uses' => 'StudyController@getStudy']);
  $router->delete('study/{id}',                                   ['middleware' => 'requires:REMOVE_STUDY',             'uses' => 'StudyController@removeStudy']);
  $router->put('study/{id}',                                      ['middleware' => 'requires:EDIT_STUDY',               'uses' => 'StudyController@updateStudy']);
  $router->get('study',                                           ['middleware' => 'requires:VIEW_STUDIES',             'uses' => 'StudyController@getAllStudies']);
  $router->post('study',                                          ['middleware' => 'requires:ADD_STUDY',                'uses' => 'StudyController@createStudy']);
  $router->post('study/{study_id}/locales/{locale_id}',           ['middleware' => 'requires:EDIT_STUDY',               'uses' => 'StudyController@saveLocale']);
  $router->delete('study/{study_id}/locales/{locale_id}',         ['middleware' => 'requires:EDIT_STUDY',               'uses' => 'StudyController@deleteLocale']);

  //* User Controller Routes *//
  $router->get('user/me',                                         [                                                     'uses' => 'UserController@getMe']);
  $router->get('user/{id}',                                       ['middleware' => 'requires:VIEW_USERS',               'uses' => 'UserController@getUser']);
  $router->delete('user/{id}',                                    ['middleware' => 'requires:REMOVE_USER',              'uses' => 'UserController@removeUser']);
  $router->get('user',                                            ['middleware' => 'requires:VIEW_USERS',               'uses' => 'UserController@getUsersPage']);
  $router->post('user/{user_id}/studies/{study_id}',              ['middleware' => 'requires:EDIT_USER',                'uses' => 'UserController@addStudy']);
  $router->delete('user/{user_id}/studies/{study_id}',            ['middleware' => 'requires:EDIT_USER',                'uses' => 'UserController@deleteStudy']);
  $router->put('user/{user_id}/update-password',                  ['middleware' => 'requires:EDIT_PASSWORDS',           'uses' => 'UserController@updatePassword']);
  $router->post('user',                                           ['middleware' => 'requires:ADD_USER',                 'uses' => 'UserController@createUser']);
  $router->put('user/{id}',                                       ['middleware' => 'requires:EDIT_USER',                'uses' => 'UserController@updateUser']);

  //* Locale Controller Routes *//
  $router->get('locale/{id}',                                     ['middleware' => 'requires:VIEW_LOCALES',             'uses' =>'LocaleController@getLocale']);
  $router->delete('locale/{id}',                                  ['middleware' => 'requires:REMOVE_LOCALE',            'uses' =>'LocaleController@removeLocale']);
  $router->post('locale/{id}',                                    ['middleware' => 'requires:EDIT_LOCALE',              'uses' =>'LocaleController@updateLocale']);
  $router->get('locale',                                          ['middleware' => 'requires:VIEW_LOCALES',             'uses' =>'LocaleController@getAllLocales']);
  $router->put('locale',                                          ['middleware' => 'requires:ADD_LOCALE',               'uses' =>'LocaleController@createLocale']);

  //* Device Controller Routes *//
  $router->get('device/{id}',                                     ['middleware' => 'requires:VIEW_DEVICES',             'uses' => 'DeviceController@getDevice']);
  $router->delete('device/{id}',                                  ['middleware' => 'requires:REMOVE_DEVICE',            'uses' => 'DeviceController@removeDevice']);
  $router->put('device/{id}',                                     ['middleware' => 'requires:EDIT_DEVICE',              'uses' => 'DeviceController@updateDevice']);
  $router->get('device',                                          ['middleware' => 'requires:VIEW_DEVICES',             'uses' => 'DeviceController@getAllDevices']);

  //* Respondent Controller Routes *//
  $router->post('study/{studyId}/respondent/import',              ['middleware' => 'requires:IMPORT_RESPONDENTS',       'uses' => 'RespondentController@importRespondents']);
  $router->post('study/{studyId}/respondent-photo/import',        ['middleware' => 'requires:IMPORT_RESPONDENTS',       'uses' => 'RespondentController@importRespondentPhotos']);
  $router->post('study/{study_id}/respondent-tag/import',         ['middleware' => 'requires:IMPORT_RESPONDENTS',       'uses' => 'ConditionTagController@importRespondentConditionTags']);
  $router->post('study/{study_id}/respondent-geo/import',         ['middleware' => 'requires:IMPORT_RESPONDENTS',       'uses' => 'RespondentGeoController@importRespondentGeos']);
  $router->post('respondent-preload-data/import',                 ['middleware' => 'requires:IMPORT_RESPONDENTS',       'uses' => 'RespondentController@preloadRespondentData']);
  $router->get('respondent/{study_id}/count',                     ['middleware' => 'requires:VIEW_RESPONDENTS',         'uses' => 'RespondentController@getRespondentCountByStudyId']);
  $router->get('respondent/{study_id}/search',                    ['middleware' => 'requires:VIEW_RESPONDENTS',         'uses' => 'RespondentController@searchRespondentsByStudyId']);
  $router->put('respondent',                                      ['middleware' => 'requires:ADD_RESPONDENT',           'uses' => 'RespondentController@createRespondent']);
  $router->delete('respondent/{id}',                              ['middleware' => 'requires:REMOVE_RESPONDENT',        'uses' => 'RespondentController@removeRespondent']);
  $router->post('respondent/{respondent_id}/photos',              ['middleware' => 'requires:ADD_RESPONDENT_PHOTO',     'uses' => 'RespondentController@addPhoto']);
  $router->delete('respondent/{respondent_id}/photo/{photo_id}',  ['middleware' => 'requires:REMOVE_RESPONDENT_PHOTO',  'uses' => 'RespondentController@removeRespondentPhoto']);
  // NOT USED $router->post('respondent/{id}',                                [                                                     'uses' => 'RespondentController@updateRespondent']);
  // NOT USED $router->get('study/{study_id}/respondents',                  'RespondentController@getAllRespondentsByStudyId');

  //* Translation Controller Routes *//
  $router->get('translation/{translation_id}/text/{text_id}',     [                                                     'uses' => 'TranslationTextController@getTranslationText']);
  $router->delete('translation/{translation_id}',                 ['middleware' => 'requires:REMOVE_TRANSLATION',       'uses' => 'TranslationController@removeTranslation']);
  $router->delete('translation/{translation_id}/text/{text_id}',  ['middleware' => 'requires:EDIT_TRANSLATION',         'uses' => 'TranslationTextController@removeTranslationText']);
  $router->post('translation/{translation_id}/text/{text_id}',    ['middleware' => 'requires:EDIT_TRANSLATION',         'uses' => 'TranslationTextController@updateTranslationText']);
  $router->get('translation/{translation_id}/text',               [                                                     'uses' => 'TranslationTextController@getAllTranslationText']);
  $router->put('translation',                                     ['middleware' => 'requires:EDIT_TRANSLATION',         'uses' => 'TranslationController@createTranslation']);
  $router->put('translation/{translation_id}/text',               ['middleware' => 'requires:EDIT_TRANSLATION',         'uses' => 'TranslationTextController@createTranslationText']);


  //* Form Builder Routes *//
  $router->group(['middleware' => 'requires:EDIT_FORM'], function () use ($router) {

    //* Question Group Controller Routes *//
    $router->get('form/section/group/{group_id}/question/',                                                                       'QuestionGroupController@getQuestionGroup');
    $router->delete('form/section/group/{group_id}',                                                                              'QuestionGroupController@removeQuestionGroup');
    $router->get('form/{form_id}/section/group/locale/{locale_id}',                                                               'QuestionGroupController@getAllQuestionGroups');
    $router->put('form/section/{section_id}/group/question',                                                                      'QuestionGroupController@createQuestionGroup');
    $router->post('form/section/group/{group_id}/question/',                                                                      'QuestionGroupController@updateQuestionGroup');
    $router->patch('form/section/groups',                                                                                         'QuestionGroupController@updateSectionQuestionGroups');

    //* Section Controller Routes *//
    $router->get('form/section/{section_id}',                                                                                     'SectionController@getSection');
    $router->delete('form/section/{section_id}',                                                                                  'SectionController@removeSection');
    $router->post('form/section/{section_id}',                                                                                    'SectionController@updateSection');
    $router->get('form/{form_id}/section/locale/{locale_id}',                                                                     'SectionController@getAllSections');
    $router->put('form/{form_id}/section',                                                                                        'SectionController@createSection');
    $router->patch('form/sections',                                                                                               'SectionController@updateSections');

    //* Form Section Controller Routes *//
    $router->post('form_section/{form_section_id}',                                                                               'FormSectionController@updateFormSection');

    //* Question Condition Controller Routes *//
    $router->put('form/section/group/question/condition/logic',                                                                   'ConditionController@editConditionLogic');
    $router->put('form/section/group/question/condition/scope',                                                                   'ConditionController@editConditionScope');
    $router->put('form/section/group/question/condition/tag',                                                                     'ConditionController@createCondition');
    $router->get('form/section/group/question/condition/tag',                                                                     'ConditionController@getAllConditions');
    $router->get('form/section/group/question/condition/tag/unique',                                                              'ConditionController@getAllUniqueConditions');
    $router->post('form/section/group/question/condition/tag/search',                                                             'ConditionController@searchAllConditions');
    $router->put('question/{question_id}/assign_condition_tag',                                                                   'QuestionController@createAssignConditionTag');
    $router->post('question/{question_id}/assign_condition_tag',                                                                  'QuestionController@updateAssignConditionTag');
    $router->delete('form/section/group/question/condition/{id}',                                                                 'ConditionController@deleteAssignConditionTag');

    //* Skip Controller Routes *//
    $router->put('form/section/group/skip/',                                                                                      'SkipController@createQuestionGroupSkip');
    $router->post('form/section/group/skip/{id}',                                                                                 'SkipController@updateSkip');
    $router->delete('form/section/group/skip/{id}',                                                                               'SkipController@deleteQuestionGroupSkip');
    $router->get('form/section/group/skip/',                                                                                      'SkipController@getAllQuestionGroupSkips');

    $router->post('form/{form_id}/skip',                                                                                          'SkipController@createFormSkip');
    $router->put('skip/{skip_id}',                                                                                                'SkipController@updateSkip');
    $router->delete('form/{form_id}/skip/{skip_id}',                                                                              'SkipController@deleteFormSkip');

    //* Question Controller Routes *//
    $router->put('form/section/group/{group_id}/question/',                                                                       'QuestionController@createQuestion');
    $router->post('form/section/group/{group_id}/question/{question_id}',                                                         'QuestionController@moveQuestion');
    $router->delete('form/section/group/question/{question_id}',                                                                  'QuestionController@removeQuestion');
    $router->get('form/section/group/question/{question_id}',                                                                     'QuestionController@getQuestion');
    $router->get('form/{form_id}/section/group/question/locale/{locale_id}',                                                      'QuestionController@getAllQuestions');
    $router->post('form/section/group/question/{question_id}',                                                                    'QuestionController@updateQuestion');
    $router->patch('form/section/group/questions',                                                                                'QuestionController@updateQuestions');
    $router->patch('form/section/group/question/choices',                                                                         'QuestionController@updateChoices');

    //* Question Type Controller Routers *//
    $router->put('question/type',                                                                                                 'QuestionTypeController@createQuestionType');
    $router->delete('question/type/{question_type_id}',                                                                           'QuestionTypeController@removeQuestionType');
    $router->get('question/type/{question_type_id}',                                                                              'QuestionTypeController@getQuestionType');
    $router->get('question/type',                                                                                                 'QuestionTypeController@getAllQuestionTypes');
    $router->post('question/type/{question_type_id}',                                                                             'QuestionTypeController@updateQuestionType');

    //* Question Choice Controller Routes *//
    $router->put('form/section/group/question/{question_id}/choice',                                                              'QuestionChoiceController@createNewQuestionChoice');
    $router->delete('form/section/group/question/choice/{question_choice_id}',                                                    'QuestionChoiceController@removeQuestionChoice');
    $router->delete('form/section/group/question/{question_id}/choice/{choice_id}',                                               'QuestionChoiceController@removeChoice');
    $router->get('form/section/group/question/choice/{choice_id}',                                                                'QuestionChoiceController@getQuestionChoice');
    $router->get('form/{form_id}/section/group/question/choice/locale/{locale_id}',                                               'QuestionChoiceController@getAllQuestionChoices');
    $router->post('form/section/group/question/choice/{choice_id}',                                                               'QuestionChoiceController@updateQuestionChoice');
    $router->post('form/section/group/question/{question_id}/choices',                                                            'QuestionChoiceController@updateQuestionChoices');

    //* Question Param Controller Routes *//
    $router->post('form/section/group/question/{question_id}/type/numeric',                                                       'QuestionParamController@updateQuestionNumeric');
    $router->post('form/section/group/question/{question_id}/type/multiple',                                                      'QuestionController@updateQuestionTypeMultiple');
    $router->post('form/section/group/question/{question_id}/type/datetime',                                                      'QuestionParamController@updateQuestionDateTime');
    $router->get('form/parameter/types',                                                                                          'QuestionParamController@getParameterTypes');
    $router->post('form/section/group/question/{question_id}/parameter',                                                          'QuestionParamController@createOrUpdateParameter');
    $router->delete('parameter/{parameter_id}',                                                                                   'QuestionParamController@deleteQuestionParameter');

  });

  //* Geo Controller Routes *//
  $router->post('study/{study_id}/geo-photo/import',           ['middleware' => 'requires:IMPORT_GEOS',                 'uses' => 'GeoController@importGeoPhotos']);
  $router->post('study/{study_id}/geo/import',                 ['middleware' => 'requires:IMPORT_GEOS',                 'uses' => 'GeoController@importGeos']);
  $router->get('geo/id/{geo_id}',                              [                                                        'uses' => 'GeoController@getGeo']);
  $router->get('geo/id/locale/{locale_id}',                    [                                                        'uses' => 'GeoController@getAllGeos']);
  $router->get('study/{study_id}/geo',                         [                                                        'uses' => 'GeoController@getAllGeosByStudyId']);
  $router->get('study/{study_id}/geo/count',                   [                                                        'uses' => 'GeoController@getGeoCountByStudyId']);
  $router->put('geo/id/locale/{locale_id}',                    ['middleware' => 'requires:ADD_GEO',                     'uses' => 'GeoController@createGeo']);
  $router->delete('geo/id/{geo_id}',                           ['middleware' => 'requires:REMOVE_GEO',                  'uses' => 'GeoController@removeGeo']);
  $router->post('geo/id/{geo_id}',                             ['middleware' => 'requires:EDIT_GEO',                    'uses' => 'GeoController@updateGeo']);

  //* Geo Type Controller Routes *//
  $router->get('geo/type/{geo_type_id}',                       [                                                        'uses' => 'GeoTypeController@getGeoType']);
  $router->get('geo/type',                                     [                                                        'uses' => 'GeoTypeController@getAllGeoTypes']);
  $router->get('study/{study_id}/geo/type',                    [                                                        'uses' => 'GeoTypeController@getAllGeoTypesByStudyId']);
  $router->get('geo/type/{parent_geo_id}/parent',              [                                                        'uses' => 'GeoTypeController@getAllEligibleGeoTypesOfParentGeo']);
  $router->post('study/{study_id}/geo/type',                   ['middleware' => 'requires:ADD_GEO_TYPE' ,               'uses' => 'GeoTypeController@createGeoType']);
  $router->delete('geo/type/{geo_type_id}',                    ['middleware' => 'requires:REMOVE_GEO_TYPE' ,            'uses' => 'GeoTypeController@removeGeoType']);
  $router->put('geo/type/{geo_type_id}',                       ['middleware' => 'requires:EDIT_GEO_TYPE' ,              'uses' => 'GeoTypeController@updateGeoType']);

  //* Config Controller Routes *//
  $router->put('config',                                       ['middleware' => 'requires:EDIT_CONFIG',                 'uses' => 'ConfigController@set']);
  $router->delete('config',                                    ['middleware' => 'requires:EDIT_CONFIG',                 'uses' => 'ConfigController@reset']);

  //* Reporting Controller Routes *//
  $router->get('study/{study_id}/reports/latest',              ['middleware' => 'requires:VIEW_REPORTS',                'uses' => 'ReportController@getLatestStudyReports']);
  $router->post('study/{study_id}/reports/dispatch',           ['middleware' => 'requires:VIEW_REPORTS',                'uses' => 'ReportController@dispatchReports']);
  $router->get('study/{study_id}/reports/download',            ['middleware' => 'requires:VIEW_REPORTS',                'uses' => 'ReportController@downloadReports']);
  $router->get('study/{study_id}/reports/{r_ids}',             ['middleware' => 'requires:VIEW_REPORTS',                'uses' => 'ReportController@getReports']);

  //* Sync Admin *//
  $router->get('list-uploads',                                ['middleware' => 'requires:VIEW_SYNC',                    'uses' => 'SyncControllerV2@listUploads']);
  $router->get('list-snapshots',                              ['middleware' => 'requires:VIEW_SYNC',                    'uses' => 'SyncControllerV2@listSnapshots']);
  $router->post('generate-snapshot',                          ['middleware' => 'requires:ADD_SNAPSHOT',                 'uses' => 'SyncControllerV2@generateSnapshot']);
  $router->post('process-uploads',                            ['middleware' => 'requires:PROCESS_UPLOADS',              'uses' => 'SyncControllerV2@processUploads']);
  $router->get('upload-log/{upload_id}',                      [                                                         'uses' => 'UploadLogController@getUploadLogs']);

  //* Group Tag Type Controller Routes *//
// NOT USED   $router->delete('group_tag_type/{id}',                          'GroupTagTypeController@removeGroupTagType');
// NOT USED   $router->get('group_tag_type',                                  'GroupTagTypeController@getAllGroupTagTypes');
// NOT USED   $router->put('group_tag_type',                                  'GroupTagTypeController@createGroupTagType');

  //* Interview Controller Routes *//
//  NOT USED      $router->get('study/{id}/interview',                            'InterviewController@getInterviewPage');
//  NOT USED      $router->get('study/{id}/interview/count',                      'InterviewController@getInterviewCount');


  // NOT USED   $router->get('report/completed',                             ['middleware' => 'requires:', 'uses' => 'ReportController@getAllSavedReports']);
// NOT USED   $router->get('report/download/{file_name}',                  ['middleware' => 'requires:', 'uses' => 'ReportController@downloadFile']);
// NOT USED   $router->get('report/{report_id}',                           ['middleware' => 'requires:', 'uses' => 'ReportController@getReport']);
// NOT USED   $router->get('report/{report_id}/status',                    ['middleware' => 'requires:', 'uses' => 'ReportController@getReportStatus']);
// NOT USED   $router->post('report/images',                               ['middleware' => 'requires:', 'uses' => 'PhotoController@getZipPhotos']);
// NOT USED   $router->post('report/clean',                                ['middleware' => 'requires:', 'uses' => 'ReportController@cleanReports']);
// NOT USED    $router->post('report/form/{form_id}',                       ['middleware' => 'requires:', 'uses' => 'ReportController@dispatchFormReport']);
// NOT USED    $router->post('report/study/{study_id}/respondents',         ['middleware' => 'requires:', 'uses' => 'ReportController@dispatchRespondentReport']);
// NOT USED    $router->post('report/study/{study_id}/edges',               ['middleware' => 'requires:', 'uses' => 'ReportController@dispatchEdgesReport']);
// NOT USED    $router->post('report/study/{study_id}/geo',                 ['middleware' => 'requires:', 'uses' => 'ReportController@dispatchGeoReport']);
// NOT USED    $router->post('report/study/{study_id}/interview',           ['middleware' => 'requires:', 'uses' => 'ReportController@dispatchInterviewReport']);
// NOT USED    $router->post('report/study/{study_id}/actions',             ['middleware' => 'requires:', 'uses' => 'ReportController@dispatchActionsReport']);

});
