<?php

namespace app\Services;

use App\Models\QuestionGroup;


class QuestionGroupService
{

    public static function getAllQuestionGroups()
    {
        $questionGroups = QuestionGroup::get();

        return $questionGroups;
    }

}