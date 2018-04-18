<?php

namespace App\Services;

use Ramsey\Uuid\Uuid;
use App\Models\Respondent;
use App\Models\StudyRespondent;

class RespondentService
{
    public static function createRespondent ($respondentName, $studyId, $assignedId = "")
    {
        $respondentId = Uuid::uuid4();

        $newRespondentModel = new Respondent;
        $newRespondentModel->id = $respondentId;
        $newRespondentModel->name = $respondentName;
        $newRespondentModel->assigned_id = $assignedId;
        $newRespondentModel->save();

        $studyRespondentId = Uuid::uuid4();
        $newStudyRespondentModel = new StudyRespondent;
        $newStudyRespondentModel->id = $studyRespondentId;
        $newStudyRespondentModel->respondent_id = $respondentId;
        $newStudyRespondentModel->study_id = $studyId;
        $newStudyRespondentModel->save();

        return $newRespondentModel;
    }
}
