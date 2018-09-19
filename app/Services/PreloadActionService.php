<?php

namespace App\Services;

use App\Models\PreloadAction;
use App\Models\Roster;
use Ramsey\Uuid\Uuid;

class PreloadActionService
{
    public static function preloadAddRosterRow ($respondentId, $questionId, $payload)
    {
        // Create roster row
        $rosterId = Uuid::uuid4();
        $rosterModel = new Roster;
        $rosterModel->id = $rosterId;
        $rosterModel->val = $payload;
        $rosterModel->save();

        // Create preload action
        $preloadActionId = Uuid::uuid4();
        $preloadActionModel = new PreloadAction;
        $preloadActionModel->id = $preloadActionId;
        $preloadActionModel->action_type = 'add-roster-row';
        $preloadActionModel->respondent_id = $respondentId;
        $preloadActionModel->question_id = $questionId;
        $preloadActionModel->payload = '{"roster_id":"' . $rosterId . '"}';
        $preloadActionModel->save();

        return $preloadActionModel;
    }
}
