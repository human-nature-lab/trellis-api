<?php

namespace app\Services;


use App\Models\QuestionType;

class QuestionTypeService
{
    public static function getAllQuestionTypes()
    {
        $questionTypes = QuestionType::all();

        return $questionTypes;
    }

}