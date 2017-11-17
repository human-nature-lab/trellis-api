<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    // Extend default dispatch method to obey USE_JOB_QUEUE env var. Defaults to using the existing php process
    public function dispatch($job){

        $envVar = getenv('USE_JOB_QUEUE');
        $useJobQueue = $envVar === '1' || strtolower($envVar) === 'true' ? true : false;
        if($useJobQueue) {
            parent::dispatch($job);
        } else {
            $job->handle();
        }

    }

}
