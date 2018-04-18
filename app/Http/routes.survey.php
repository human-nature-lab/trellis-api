<?php

//***************************//
//* Token Controller Routes *//
//***************************//


use Illuminate\Support\Facades\Artisan;


$app->group([
    'namespace' => 'App\Http\Controllers',
    'prefix' => 'survey-view'
], function ($app) {
    $app->get('form/{form_id}', 'SurveyViewController@showLogin');

    $app->post(
        'form/{formId}/login',
        'InterviewController@selfAdministeredLogin'
    );
});