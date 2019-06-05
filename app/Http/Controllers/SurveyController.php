<?php namespace App\Http\Controllers;

use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Log;
use Ramsey\Uuid\Uuid;
use Validator;

class SurveyController extends Controller {

    /**
     * Display a listing of the resource.
     * GET /survey
     *
     * @return Response
     */
    public function index()
    {
        return response()->json([
            'surveys' => Survey::all()
        ], Response::HTTP_OK);
    }

    /**
     * @param $surveyId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSurveyById ($surveyId) {
        $surveyId = urldecode($surveyId);
        $validator = Validator::make([
            'surveyId' => $surveyId
        ], [
            'surveyId' => 'required|string|min:36|exists:survey,id'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors()
            ], $validator->statusCode());
        }

        return response()->json([
            'survey' => Survey::find($surveyId)
        ], Response::HTTP_OK);
    }

    /**
     * Get a single study object
     * @param {string} $studyId
     * @param {string} $respondentId
     * @param {string} $formId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStudySurveyByFormId ($studyId, $respondentId, $formId) {
        $studyId = urldecode($studyId);
        $respondentId = urldecode($respondentId);
        $formId = urldecode($formId);

        $validator = Validator::make([
            'studyId' => $studyId,
            'respondentId' => $respondentId,
            'formId' => $formId
        ], [
            'studyId' => 'required|string|min:36|exists:study,id',
            'respondentId' => 'required|string|min:36|exists:respondent,id',
            'formId' => 'required|string|min:36|exists:form,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors()
            ], $validator->statusCode());
        }

        $survey = Survey::query()
            ->where('study_id', $studyId)
            ->where('respondent_id', $respondentId)
            ->where('form_id', $formId)
            ->with('interviews')
            ->first();

        return response()->json([
            'survey' => $survey
        ], Response::HTTP_OK);
    }

    /**
     * Get all surveys completed by the respondent in this study
     * @param {String} $studyId
     * @param {String} $respondentId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getRespondentStudySurveys($studyId, $respondentId) {
        $respondentId = urldecode($respondentId);
        $studyId = urldecode($studyId);
        $validator = Validator::make([
            'study' => $studyId,
            'respondent' => $respondentId
        ], [
            'study' => 'required|string|min:36|exists:study,id',
            'respondent' => 'required|string|min:36|exists:respondent,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors()
            ], $validator->statusCode());
        }

        $q = Survey::where('respondent_id', $respondentId)
            ->where('study_id', $studyId)
            ->with('interviews');

        Log::debug($q->toSql());

        return response()->json([
            'surveys' => $q->get()
        ], Response::HTTP_OK);
    }

    public function getStudySurveys (Request $request, $studyId) {
        $validator = Validator::make(array_merge($request->all(), [
            'studyId' => $studyId
        ]), [
            'respondent_id' => 'nullable|string|min:32|exists:respondent,id',
            'studyId' => 'required|string|min:32|exists:study,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => "Validation failed",
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $respondentId = $request->get('respondent_id');
        $surveys = Survey::where('study_id', '=', $studyId)
            ->whereNull('deleted_at')->get();

        if ($respondentId !== null) {
            $surveys = $surveys->where('respondent_id', '=', $respondentId);
        }

        return response()->json([
            'surveys' => $surveys
        ], Response::HTTP_OK);
    }

    /**
     * Create a new survey for the specified form
     * POST /survey
     *
     * @return Response
     */
    public function createSurvey ($studyId, $respondentId, $formId)
    {
        $studyId = urldecode($studyId);
        $respondentId = urldecode($respondentId);
        $formId = urldecode($formId);

        $validator = Validator::make([
            'study' => $studyId,
            'respondent' => $respondentId,
            'form' => $formId
        ], [
            'study' => 'required|string|min:36|exists:study,id',
            'respondent' => 'required|string|min:36|exists:respondent,id',
            'form' => 'required|string|min:36|exists:form,id'
        ]);

        if($validator->fails()){
            return response()->json([
                'msg' => $validator->errors()
            ], $validator->statusCode());
        }

        $survey = Survey::create([
            'id' => Uuid::uuid4(),
            'respondent_id' => $respondentId,
            'form_id' => $formId,
            'study_id' => $studyId
        ]);

        return response()->json([
            'survey' => $survey
        ], Response::HTTP_OK);
    }


    public function completeSurvey ($surveyId) {
        $surveyId = urldecode($surveyId);

        $validator = Validator::make([
            'surveyId' => $surveyId
        ], [
            'surveyId' => 'required|string|min:36|exists:survey,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors()
            ], $validator->statusCode());
        }

        $survey = Survey::find($surveyId);
        $survey->completed_at = Carbon::now();
        $survey->save();

        return response()->json([
            'survey' => $survey
        ], Response::HTTP_OK);
    }
}