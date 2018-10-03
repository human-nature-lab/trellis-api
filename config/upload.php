<?php

return [
    'directory' => [
        'pending' => env('UPLOADS_PENDING_DIRECTORY', 'uploads-pending'),
        'running' => env('UPLOADS_RUNNING_DIRECTORY', 'uploads-running'),
        'success' => env('UPLOADS_SUCCESS_DIRECTORY', 'uploads-success'),
        'failed' => env('UPLOADS_FAILED_DIRECTORY', 'uploads-failed')
    ],
    'seconds' => [
        'min' => env('UPLOAD_SECONDS_MIN', 60),
    ]
];
