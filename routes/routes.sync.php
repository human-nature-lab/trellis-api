<?php

$router->group([
    'prefix' => 'sync'
], function () use ($router) {

    //**************************//
    //* Sync v2 Controller Routes *//
    //**************************//
    $router->get('heartbeat',                               'SyncControllerV2@heartbeat');

    $router->group([
        'prefix' => 'device/{device_id}',
        'middleware' => ['device', 'basic-auth']
    ], function ($router) {
        $router->get('syncv2/authenticate',                 'SyncControllerV2@authenticate');
        $router->get('syncv2/snapshot',                     'SyncControllerV2@getSnapshotInfo');
        $router->get('snapshot/{snapshot_id}/file_size',    'SyncControllerV2@getSnapshotFileSize');
        $router->get('snapshot/{snapshot_id}/download',     'SyncControllerV2@downloadSnapshot');

        $router->get('image',                               'SyncControllerV2@listImages');
//        $router->post('image',                              'SyncController@syncImages');
        $router->get('image/{file_name}',                   'SyncControllerV2@getImage');
        $router->post('image_size',                         'SyncControllerV2@getImageSize');
        $router->get('missing-images',                      'SyncControllerV2@listMissingImages');

        $router->get('uploads',                             'SyncControllerV2@getPendingUploads');
        $router->post('verify-upload',                      'SyncControllerV2@verifyUpload');
        $router->post('upload',                             'SyncControllerV2@upload');
        $router->post('upload/image',                       'SyncControllerV2@uploadImage');
        $router->post('upload/logs',                        'SyncControllerV2@uploadLogs');


//        $router->put('sync',                                'SyncController@upload');
//        $router->post('sync',                               'SyncController@download');
//        $router->get('download',                            'SyncController@downloadSync');
    });

});


