<?php

$app->group([
    'namespace' => 'App\Http\Controllers',
    'prefix' => 'sync'
], function ($app) {

    //**************************//
    //* Sync v2 Controller Routes *//
    //**************************//
    $app->get(
        'heartbeat',
        'SyncController@heartbeat'
    );

    $app->get(
        'device/{device_id}/syncv2/authenticate',
        'SyncControllerV2@authenticate'
    );

    $app->get(
        'device/{device_id}/syncv2/snapshot',
        'SyncControllerV2@getSnapshotInfo'
    );

    $app->get(
        'snapshot/{snapshot_id}/file_size',
        'SyncControllerV2@getSnapshotFileSize'
    );

    $app->get(
        'snapshot/{snapshot_id}/download',
        'SyncControllerV2@downloadSnapshot'
    );
});


