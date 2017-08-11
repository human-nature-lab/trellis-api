<?php

namespace App\Listeners;

use DB;
use App\Models\Log;

class ModelEventListener
{
    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        //NOTE these could go in a BaseModel::boot() method and use Model::saving($callback, $priority = 0) etc instead

        $events->listen(
            [
                'eloquent.saving: *',
                'eloquent.deleting: *',
            ],
            Log::class . '@onModelUpdating'
        );

        $events->listen(
            [
                'eloquent.saved: *',
                'eloquent.deleted: *',
            ],
            Log::class . '@onModelUpdated'
        );
    }
}
