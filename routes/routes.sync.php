<?php

$router->group([
    'prefix' => 'sync'
], function () use ($router) {

    //**************************//
    //* Sync v2 Controller Routes *//
    //**************************//
    $router->get(
        'heartbeat',
        'SyncControllerV2@heartbeat'
    );

    $router->get(
        'device/{device_id}/syncv2/authenticate',
        'SyncControllerV2@authenticate'
    );

    $router->get(
        'device/{device_id}/syncv2/snapshot',
        'SyncControllerV2@getSnapshotInfo'
    );

    $router->get(
        'snapshot/{snapshot_id}/file_size',
        'SyncControllerV2@getSnapshotFileSize'
    );

    $router->get(
        'snapshot/{snapshot_id}/download',
        'SyncControllerV2@downloadSnapshot'
    );

    $router->get(
        'device/{device_id}/image',
        'SyncControllerV2@listImages'
    );

    $router->get(
        'device/{device_id}/image/{file_name}',
        'SyncControllerV2@getImage'
    );

    $router->post(
        'device/{device_id}/image_size',
        'SyncControllerV2@getImageSize'
    );

    $router->get(
        'device/{device_id}/uploads',
        'SyncControllerV2@getPendingUploads'
    );

    $router->post(
        'device/{device_id}/upload',
        'SyncControllerV2@upload'
    );

    $router->post(
        'device/{device_id}/verify-upload',
        'SyncControllerV2@verifyUpload'
    );
});


