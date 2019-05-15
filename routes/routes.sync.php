<?php

$router->group([
    'prefix' => 'sync'
], function () use ($router) {

    //**************************//
    //* Sync v2 Controller Routes *//
    //**************************//
    $router->get('heartbeat',                               'SyncControllerV2@heartbeat');

    $router->group(['prefix' => 'device/{device_id}', 'middleware' => ['device', 'basic-auth']], function ($router) {
        $router->get('syncv2/authenticate',                                                                     'SyncControllerV2@authenticate');
        $router->get('syncv2/snapshot',                                                                         'SyncControllerV2@getSnapshotInfo');
        $router->get('snapshot/{snapshot_id}/file_size',                                                        'SyncControllerV2@getSnapshotFileSize');
        $router->get('snapshot/{snapshot_id}/download',     ['middleware' => 'requires:CAN_DOWNLOAD', 'uses' => 'SyncControllerV2@downloadSnapshot']);

        $router->get('image',                                                                                   'SyncControllerV2@listImages');
        $router->get('image/{file_name}',                                                                       'SyncControllerV2@getImage');
        $router->post('image_size',                                                                             'SyncControllerV2@getImageSize');
        $router->get('missing-images',                                                                          'SyncControllerV2@listMissingImages');

        $router->get('uploads',                                                                                 'SyncControllerV2@getPendingUploads');
        $router->post('verify-upload',                                                                          'SyncControllerV2@verifyUpload');
        $router->post('upload',                             ['middleware' => 'requires:CAN_UPLOAD',   'uses' => 'SyncControllerV2@upload']);
        $router->post('upload/image',                                                                           'SyncControllerV2@uploadImage');
        $router->post('upload/logs',                                                                            'SyncControllerV2@uploadLogs');

//   NOT USED     $router->put('sync',                                'SyncController@upload');
//   NOT USED     $router->post('sync',                               'SyncController@download');
//   NOT USED     $router->get('download',                            'SyncController@downloadSync');
    });

});


