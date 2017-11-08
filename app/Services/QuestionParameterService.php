<?php

namespace app\Services;

use App\Models\QuestionParameter;
use Ramsey\Uuid\Uuid;
use DB;

class QuestionParameterService
{
    public static function createQuestionParameter($questionId, $parameterId, $val)
    {
        $newQuestionParameter = new QuestionParameter;
        $questionParameterId = Uuid::uuid4();
        DB::transaction(function () use ($questionId, $questionParameterId, $newQuestionParameter, $parameterId, $val) {
            $newQuestionParameter->id = $questionParameterId;
            $newQuestionParameter->parameter_id= $parameterId;
            $newQuestionParameter->question_id= $questionId;
            $newQuestionParameter->val = $val;
            $newQuestionParameter->save();
        });
        return $newQuestionParameter;
    }
}
