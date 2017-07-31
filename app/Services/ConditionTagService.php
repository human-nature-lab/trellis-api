<?php

namespace app\Services;

use App\Models\ConditionTag;
use Ramsey\Uuid\Uuid;
use DB;

class ConditionTagService
{
    public static function createConditionTag($name)
    {
        $newConditionTag = new ConditionTag;
        $conditionTagId = Uuid::uuid4();
        DB::transaction(function () use ($newConditionTag, $conditionTagId, $name) {
            $newConditionTag->id = $conditionTagId;
            $newConditionTag->name = $name;
            $newConditionTag->save();
        });
        return $newConditionTag;
    }
}
