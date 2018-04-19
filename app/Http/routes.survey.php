<?php

//***************************//
//* Token Controller Routes *//
//***************************//


use Illuminate\Support\Facades\Artisan;

$app->group([
    'namespace' => 'App\Http\Controllers',
    'prefix' => 'survey-view'
], function ($app) {
    $app->get('form/{form_id}/show',                        'SurveyViewController@showLogin');

    $app->post('form/{formId}/login',                       'InterviewController@selfAdministeredLogin');

    $app->get('interview/{interview_id}/data',              'InterviewDataController@getInterviewData');

    $app->post('interview/{interview_id}/data',             'InterviewDataController@updateInterviewData');

    $app->get('interview/{interview_id}',                   'InterviewController@getInterview');

    $app->get('form/{form_id}',                             'FormController@getForm');

});


// Old interview routes
$app->post('form/{id}/interview/{respondentId}/submit', 'InterviewController@submit');
$app->get('form/{id}',                                      'FormController@getForm');
$app->get('study/{study_id}/respondents',                   'RespondentController@getAllRespondentsByStudyId');
$app->get('study/{id}',                                     'StudyController@getStudy');
$app->get('photo/{id}',                                     'PhotoController@getPhoto');