<?php

return [

    'directory' => [
        'path' => env('TEMP_DIRECTORY_PATH', 'temp'),
        'size' => [
            'max' => env('TEMP_DIRECTORY_SIZE_MAX', 100*1024*1024),
        ],
    ],
    'seconds' => [
        'max' => env('TEMP_SECONDS_MAX', 60*60),
    ],
];
