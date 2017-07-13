<?php

return [

    'directory' => [
        'path' => env('SNAPSHOT_DIRECTORY_PATH', 'snapshot'),
        'size' => [
            'max' => env('SNAPSHOT_DIRECTORY_SIZE_MAX', 100*1024*1024),
        ],
    ],
    'seconds' => [
        'min' => env('SNAPSHOT_SECONDS_MIN', 60),
    ],

    // table fields to substitute during upload/download (the * wildcard indicates all fields).  set to null to skip
    'substitutions' => [
        'upload' => [
            'device' => [
                '*' => null,
            ],
            'epoch' => [
                '*' => null,
            ],
            'user' => [
                '*' => null,
            ],
        ],
        'download' => [
            //TODO
            // 'device' => [
            //     'device_id' => null,
            // ],
            // 'user' => [
            //     'password' => '',
            // ],
        ],
    ],
];
