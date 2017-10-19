<?php

namespace App\Services;

use App\Models\Skip;
use App\Models\QuestionGroupSkip;
use App\Models\SkipConditionTag;
use Ramsey\Uuid\Uuid;

class SkipService
{
    public static function createSkip($questionGroupId, $showHide, $anyAll, $precedence)
    {
        $skipId = Uuid::uuid4();
        $skip = new Skip;

        $skip->id = $skipId;
        $skip->show_hide = $showHide;
        $skip->any_all = $anyAll;
        $skip->precedence = $precedence;

        $skip->save();

        $questionGroupSkipId = Uuid::uuid4();
        $questionGroupSkip = new QuestionGroupSkip;
        $questionGroupSkip->id = $questionGroupSkipId;
        $questionGroupSkip->question_group_id = $questionGroupId;
        $questionGroupSkip->skip_id = $skipId;

        $questionGroupSkip->save();

        return $skip;
    }

    public static function createSkipConditionTag($skipId, $conditionTagName)
    {
        $skipConditionTagId = Uuid::uuid4();
        $skipConditionTag = new SkipConditionTag;

        $skipConditionTag->id = $skipConditionTagId;
        $skipConditionTag->skip_id = $skipId;
        $skipConditionTag->condition_tag_name = $conditionTagName;

        $skipConditionTag->save();

        return $skipConditionTag;
    }

}
