<?php

namespace App\Console\Commands;


trait AutoDispatch {
    public function dispatch($job) {
        $envVar = getenv('USE_JOB_QUEUE');
        $useJobQueue = $envVar === '1' || strtolower($envVar) === 'true' ? true : false;
        if($useJobQueue) {
            parent::dispatch($job);
        } else {
            $job->handle();
        }
    }
}