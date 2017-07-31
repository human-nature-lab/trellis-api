<?php

namespace app\Services;

use App\Models\QuestionType;
use DB;

class QuestionTypeService
{
    public static function getAllQuestionTypes()
    {
        $questionTypes = QuestionType::all();

        return $questionTypes;
    }

    public function getIdByName($questionTypeName)
    {
        $questionTypeId = DB::table('question_type')->where('name', '=', $questionTypeName)->first()->id;

        return $questionTypeId;
    }
}
