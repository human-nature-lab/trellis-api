<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    // Extend default dispatch method to obey USE_JOB_QUEUE env var. Defaults to using the existing php process
    public function dispatch($job){

        $useJobQueue = getenv('USE_JOB_QUEUE') === '1' ? true : false;
        if($useJobQueue) {
            parent::dispatch($job);
        } else {
            $job->handle();
        }

    }

}
