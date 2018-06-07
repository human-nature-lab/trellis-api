<?php

//***************************//
//* Token Controller Routes *//
//***************************//


use Illuminate\Support\Facades\Artisan;

$app->group([
    'namespace' => 'App\Http\Controllers',
    'prefix' => 'survey-view',
    'middleware' => ['key'],
], function ($app) {
    $app->post('login',                                             'TokenController@createToken');
});

$app->group([
    'namespace' => 'App\Http\Controllers',
    'prefix' => 'survey-view',
    'middleware' => ['key', 'token']
], function ($app) {

    $app->get('condition-tags/respondent',                          'ConditionController@getAllRespondentConditionTags');

//        $app->get('form/{form_id}/show',                        'SurveyViewController@showLogin');
//        $app->post('form/{formId}/login',                       'InterviewController@selfAdministeredLogin');
    $app->get('form/{form_id}',                                     'FormController@getForm');

    $app->get('interview/{i_id}/actions',                           'InterviewDataController@getInterviewActionsByInterviewId');
    $app->get('interview/{i_id}/data',                              'InterviewDataController@getInterviewDataByInterviewId');
    $app->get('interview/{i_id}/preload',                           'PreloadController@getPreloadDataByInterviewId');
    $app->get('interview/{i_id}',                                   'InterviewController@getInterview');
    $app->post('interview/{i_id}/complete',                         'InterviewController@completeInterview');
    $app->post('interview/{i_id}/actions',                          'InterviewDataController@saveInterviewActions');
    $app->post('interview/{i_id}/data',                             'InterviewDataController@updateInterviewData');

    $app->post('survey/{s_id}/interview',                           'InterviewController@createInterview');

    $app->get('study/{s_id}/respondents/search',                    'RespondentController@searchRespondentsByStudyId');
    $app->get('study/{s_id}/respondents',                           'RespondentController@getAllRespondentsByStudyId');
    $app->get('study/{s_id}',                                       'StudyController@getStudy');
    $app->get('studies',                                            'StudyController@getAllStudiesComplete');
    $app->get('study/{s_id}/respondent/{r_id}/surveys',             'SurveyController@getRespondentStudySurveys');
    $app->get('study/{s_id}/forms/published',                       'FormController@getPublishedForms');
    $app->post('study/{s_id}/respondent/{r_id}/form/{f_id}/survey', 'SurveyController@createSurvey');

    $app->get('respondent/{r_id}',                                  'RespondentController@getRespondentById');


    $app->post('edges',                                             'EdgeController@createEdges');
    $app->get('edges/{e_ids}',                                      'EdgeController@getEdgesById');

    $app->post('rosters',                                           'RosterController@createRosterRows');
    $app->get('rosters/{r_ids}',                                    'RosterController@getRostersById');
    $app->put('rosters',                                            'RosterController@editRosterRows');

    $app->get('geos/{g_ids}',                                       'GeoController@getGeosById');
    $app->get('geo/search',                                         'GeoController@searchGeos');

    $app->get('photo/{p_id}',                                       'PhotoController@getPhoto');

    $app->get('me/studies',                                         'UserController@getMyStudies');
});


// Old interview routes
$app->post('form/{id}/interview/{respondentId}/submit', 'InterviewController@submit');
$app->get('form/{id}',                                  'FormController@getForm');
$app->get('study/{id}',                                 'StudyController@getStudy');
$app->get('photo/{id}',                                 'PhotoController@getPhoto');