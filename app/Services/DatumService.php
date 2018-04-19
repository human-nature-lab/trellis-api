<?php

namespace App\Services;

use App\Models\Datum;
use App\Models\QuestionDatum;
use Ramsey\Uuid\Uuid;

class DatumService
{
    public static function createDatum ($surveyId, $questionId, $name, $val)
    {
        // Create new question_datum
        $questionDatumId = Uuid::uuid4();
        $questionDatumModel = new QuestionDatum;
        $questionDatumModel->id = $questionDatumId;
        $questionDatumModel->question_id = $questionId;
        // TODO:
        $questionDatumModel->section_repetition = 0;
        //$questionDatumModel->follow_up_datum_id = $followUpDatumId;
        $questionDatumModel->survey_id = $surveyId;
        $questionDatumModel->save();

        // Create new datum
        $datumId = Uuid::uuid4();
        $datumModel = new Datum;
        $datumModel->id = $datumId;
        $datumModel->question_datum_id = $questionDatumId;
        $datumModel->name = $name;
        $datumModel->val = $val;
        $datumModel->save();

        return $datumModel;
    }
}
