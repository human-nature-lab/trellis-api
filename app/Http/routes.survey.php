<?php

//***************************//
//* Token Controller Routes *//
//***************************//


use Illuminate\Support\Facades\Artisan;


$app->group([
    'namespace' => 'App\Http\Controllers',
    'prefix' => 'survey-view'
], function ($app) {
    $app->get('form/{form_id}/show', 'SurveyViewController@showLogin');

    $app->post('form/{formId}/login', 'InterviewController@selfAdministeredLogin');

    $app->post('interview/{interview_id}/data', 'InterviewDataController@updateInterviewData');

    $app->get('interview/{interview_id}', 'InterviewController@getInterview');

    $app->get('form/{form_id}', 'FormController@getForm');
});