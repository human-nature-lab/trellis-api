<?php

$router->group(['middleware' => 'demo'], function () use ($router) {

    $router->post('demo/confirm/{id}',                        'DemoController@confirmEmail');
    $router->post('demo/create-user',                         'DemoController@createUserConfirmation');
    $router->get('demo/preview/confirmation/{name}',          'DemoController@previewConfirmation');

});