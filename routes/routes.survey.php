<?php

//***************************//
//* Token Controller Routes *//
//***************************//


use Illuminate\Support\Facades\Artisan;

$router->group([
    'prefix' => 'survey-view',
    'middleware' => ['key'],
], function () use ($router) {
    $router->post('login',                                             'TokenController@createToken');
});

$router->group([
    'prefix' => 'survey-view',
    'middleware' => ['key', 'token']
], function () use ($router) {

    $router->get('condition-tags/respondent',                          'ConditionController@getAllRespondentConditionTags');

    $router->get('form/{form_id}',                                     'FormController@getForm');

    $router->group([
        'prefix' => 'interview/{i_id}'
    ], function () use ($router) {
        $router->get('actions',                                 'InterviewDataController@getInterviewActionsByInterviewId');
        $router->get('data',                                    'InterviewDataController@getInterviewDataByInterviewId');
        $router->get('preload',                                 'PreloadController@getPreloadDataByInterviewId');
        $router->get('/',                                       'InterviewController@getInterview');
        $router->post('complete',                               'InterviewController@completeInterview');
        $router->post('actions',                                'InterviewDataController@saveInterviewActions');
        $router->post('data',                                   'InterviewDataController@updateInterviewData');
    });

    $router->post('survey/{s_id}/interview',                           'InterviewController@createInterview');

    $router->get('studies',                                            'StudyController@getAllStudiesComplete');
    $router->group([
        'prefix' => 'study/{s_id}'
    ], function () use ($router) {
        $router->post('respondent/{r_id}/form/{f_id}/survey', 'SurveyController@createSurvey');
        $router->get('respondents/search',                    'RespondentController@searchRespondentsByStudyId');
        $router->get('respondents',                           'RespondentController@getAllRespondentsByStudyId');
        $router->get('/',                                     'StudyController@getStudy');
        $router->get('respondent/{r_id}/surveys',             'SurveyController@getRespondentStudySurveys');
        $router->get('forms/published',                       'FormController@getPublishedForms');
    });


    $router->get('respondent/{r_id}',                                  'RespondentController@getRespondentById');
    $router->get('respondent/{r_id}/fills',                            'RespondentController@getRespondentFillsById');


    $router->post('edges',                                             'EdgeController@createEdges');
    $router->get('edges/{e_ids}',                                      'EdgeController@getEdgesById');

    $router->post('rosters',                                           'RosterController@createRosterRows');
    $router->get('rosters/{r_ids}',                                    'RosterController@getRostersById');
    $router->put('rosters',                                            'RosterController@editRosterRows');

    $router->get('geos/{g_ids}',                                       'GeoController@getGeosById');
    $router->get('geo/search',                                         'GeoController@searchGeos');

    $router->get('photo/{p_id}',                                       'PhotoController@getPhoto');

    $router->get('me/studies',                                         'UserController@getMyStudies');
});