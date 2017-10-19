<?php

namespace app\Services;

use App\Models\AssignConditionTag;
use App\Models\QuestionAssignConditionTag;
use Ramsey\Uuid\Uuid;
use DB;

class AssignConditionTagService
{
    public static function createAssignConditionTag($questionId, $conditionTagId, $logic, $scope)
    {
        $newAssignConditionTag = new AssignConditionTag;
        $assignConditionTagId = Uuid::uuid4();
        $questionAssignConditionTagId = Uuid::uuid4();


        DB::transaction(function () use ($newAssignConditionTag, $assignConditionTagId, $questionAssignConditionTagId, $questionId, $conditionTagId, $logic, $scope) {
            $newAssignConditionTag->id = $assignConditionTagId;
            $newAssignConditionTag->condition_tag_id = $conditionTagId;
            $newAssignConditionTag->logic = $logic;
            $newAssignConditionTag->scope = $scope;

            $newAssignConditionTag->save();

            $newQuestionAssignConditionTag = new QuestionAssignConditionTag;
            $newQuestionAssignConditionTag->id = $questionAssignConditionTagId;
            $newQuestionAssignConditionTag->question_id = $questionId;
            $newQuestionAssignConditionTag->assign_condition_tag_id = $assignConditionTagId;

            $newQuestionAssignConditionTag->save();
        });
        return $newAssignConditionTag;
    }
}
