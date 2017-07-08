<?php

return [

    'directory' => env('SNAPSHOT_DIRECTORY', 'snapshot'),
    'seconds' => [
        'min' => env('SNAPSHOT_SECONDS_MIN', 60),
    ],

    // table fields to substitute during upload/download (the * wildcard indicates all fields).  set to null to skip
    'substitutions' => [
        'upload' => [
            'device' => [
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
