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

    'sqliteSchema' => __dir__ . '/../database/base.sqlite.schema.sql',
    'sqliteIndex' => __dir__ . '/../database/base.sqlite.indexes.sql',

    'ignoredTables' => [
      'client_log',
      'device',
      'failed_jobs',
      'jobs',
      'key',
      'log',
      'migrations',
      'report',
      'report_file',
      'snapshot',
      'sync',
      'token',
      'upload',
      'upload_log',
      'user_confirmation',
    ],

];
