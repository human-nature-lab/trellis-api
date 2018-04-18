<?php

namespace App\Services;

use App\Models\Survey;
use App\Models\SelfAdministeredSurvey;
use Ramsey\Uuid\Uuid;

class SelfAdministeredSurveyService
{
    public static function assignSurvey ($respondentId, $formId, $studyId, $password)
    {
        $surveyId = Uuid::uuid4();

        $newSurveyModel = new Survey;
        $newSurveyModel->id = $surveyId;
        $newSurveyModel->respondent_id = $respondentId;
        $newSurveyModel->form_id = $formId;
        $newSurveyModel->study_id = $studyId;
        $newSurveyModel->save();

        $sasModel = new SelfAdministeredSurvey;

        $sasId = Uuid::uuid4();
        $sasModel->id = $sasId;
        $sasModel->survey_id = $surveyId;
        $sasModel->password = $password;
        $sasModel->save();

        return $sasModel;
    }
}
