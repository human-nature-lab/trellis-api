<?php

$router->group([
    'prefix' => 'sync'
], function () use ($router) {

    //**************************//
    //* Sync v2 Controller Routes *//
    //**************************//
    $router->get(
        'heartbeat',
        'SyncController@heartbeat'
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
});


