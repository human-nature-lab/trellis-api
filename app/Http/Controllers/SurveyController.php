<?php namespace App\Http\Controllers;

use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class SurveyController extends Controller
{
  /**
   * Display a listing of the resource.
   * GET /survey
   *
   * @return Response
   */
  public function index()
  {
    return response()->json(
      [
        "surveys" => Survey::all(),
      ],
      Response::HTTP_OK,
    );
  }

  /**
   * @param $surveyId
   * @return \Illuminate\Http\JsonResponse
   */
  public function getSurveyById($surveyId)
  {
    $surveyId = urldecode($surveyId);
    $validator = Validator::make(
      [
        "surveyId" => $surveyId,
      ],
      [
        "surveyId" => "required|string|min:36|exists:survey,id",
      ],
    );
    if ($validator->fails()) {
      return response()->json(
        [
          "msg" => $validator->errors(),
        ],
        $validator->statusCode(),
      );
    }

    $survey = Survey::with('form')->find($surveyId);

    return response()->json(
      [
        "survey" => $survey,
      ],
      Response::HTTP_OK,
    );
  }

  /**
   * Get a single study object
   * @param {string} $studyId
   * @param {string} $respondentId
   * @param {string} $formId
   * @return \Illuminate\Http\JsonResponse
   */
  public function getStudySurveyByFormId($studyId, $respondentId, $formId)
  {
    $studyId = urldecode($studyId);
    $respondentId = urldecode($respondentId);
    $formId = urldecode($formId);

    $validator = Validator::make(
      [
        "studyId" => $studyId,
        "respondentId" => $respondentId,
        "formId" => $formId,
      ],
      [
        "studyId" => "required|string|min:36|exists:study,id",
        "respondentId" => "required|string|min:36|exists:respondent,id",
        "formId" => "required|string|min:36|exists:form,id",
      ],
    );

    if ($validator->fails()) {
      return response()->json(
        [
          "msg" => $validator->errors(),
        ],
        $validator->statusCode(),
      );
    }

    $survey = Survey::query()
      ->where("study_id", $studyId)
      ->where("respondent_id", $respondentId)
      ->where("form_id", $formId)
      ->with("interviews")
      ->first();

    return response()->json(
      [
        "survey" => $survey,
      ],
      Response::HTTP_OK,
    );
  }

  /**
   * Get all surveys completed by the respondent in this study
   * @param {String} $studyId
   * @param {String} $respondentId
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function getRespondentStudySurveys($studyId, $respondentId)
  {
    $respondentId = urldecode($respondentId);
    $studyId = urldecode($studyId);
    $validator = Validator::make(
      [
        "study" => $studyId,
        "respondent" => $respondentId,
      ],
      [
        "study" => "required|string|min:36|exists:study,id",
        "respondent" => "required|string|min:36|exists:respondent,id",
      ],
    );

    if ($validator->fails()) {
      return response()->json(
        [
          "msg" => $validator->errors(),
        ],
        $validator->statusCode(),
      );
    }

    $q = Survey::where("respondent_id", $respondentId)
      ->where("study_id", $studyId)
      ->with("interviews", "form");

    return response()->json(
      [
        "surveys" => $q->get(),
      ],
      Response::HTTP_OK,
    );
  }

  public function getStudySurveys(Request $request, $studyId)
  {
    $validator = Validator::make(
      array_merge($request->all(), [
        "studyId" => $studyId,
      ]),
      [
        "respondent_id" => "nullable|string|min:32|exists:respondent,id",
        "studyId" => "required|string|min:32|exists:study,id",
      ],
    );

    if ($validator->fails()) {
      return response()->json(
        [
          "msg" => "Validation failed",
          "err" => $validator->errors(),
        ],
        $validator->statusCode(),
      );
    }

    $respondentId = $request->get("respondent_id");
    $surveys = Survey::where("study_id", "=", $studyId)
      ->whereNull("deleted_at")
      ->get();

    if ($respondentId !== null) {
      $surveys = $surveys->where("respondent_id", "=", $respondentId);
    }

    return response()->json(
      [
        "surveys" => $surveys,
      ],
      Response::HTTP_OK,
    );
  }

  /**
   * Create a new survey for the specified form
   * POST /survey
   *
   * @return Response
   */
  public function createSurvey($studyId, $respondentId, $formId)
  {
    $studyId = urldecode($studyId);
    $respondentId = urldecode($respondentId);
    $formId = urldecode($formId);

    $validator = Validator::make(
      [
        "study" => $studyId,
        "respondent" => $respondentId,
        "form" => $formId,
      ],
      [
        "study" => "required|string|min:36|exists:study,id",
        "respondent" => "required|string|min:36|exists:respondent,id",
        "form" => "required|string|min:36|exists:form,id",
      ],
    );

    if ($validator->fails()) {
      return response()->json(
        [
          "msg" => $validator->errors(),
        ],
        $validator->statusCode(),
      );
    }

    $survey = Survey::create([
      "id" => Uuid::uuid4(),
      "respondent_id" => $respondentId,
      "form_id" => $formId,
      "study_id" => $studyId,
    ]);

    return response()->json(
      [
        "survey" => $survey,
      ],
      Response::HTTP_OK,
    );
  }

  public function completeSurvey($surveyId)
  {
    $surveyId = urldecode($surveyId);

    $validator = Validator::make(
      [
        "surveyId" => $surveyId,
      ],
      [
        "surveyId" => "required|string|min:36|exists:survey,id",
      ],
    );

    if ($validator->fails()) {
      return response()->json(
        [
          "msg" => $validator->errors(),
        ],
        $validator->statusCode(),
      );
    }

    $survey = Survey::find($surveyId);
    $survey->completed_at = Carbon::now();
    $survey->save();

    return response()->json(
      [
        "survey" => $survey,
      ],
      Response::HTTP_OK,
    );
  }

  public function uncompleteSurvey(string $surveyId)
  {
    $surveyId = urldecode($surveyId);

    $validator = Validator::make(
      [
        "surveyId" => $surveyId,
      ],
      [
        "surveyId" => "required|string|min:36|exists:survey,id",
      ],
    );

    if ($validator->fails()) {
      return response()->json(
        [
          "msg" => $validator->errors(),
        ],
        $validator->statusCode(),
      );
    }

    $survey = Survey::find($surveyId);
    $survey->completed_at = null;
    $survey->save();

    return response()->json(
      [
        "survey" => $survey,
      ],
      Response::HTTP_OK,
    );
  }

  /**
   * Automate the transfer of a survey from one respondent to another
   */
  public function transferSurvey(Request $request, string $surveyId)
  {
    $surveyId = urldecode($surveyId);
    $newRespondentId = $request->get('newRespondentId');
    $validator = Validator::make(
      [
        "surveyId" => $surveyId,
        "newRespondentId" => $newRespondentId,
      ],
      [
        "surveyId" => "required|string|min:36|exists:survey,id",
        "newRespondentId" => "required|string|min:36|exists:respondent,id",
      ],
    );
    if ($validator->fails()) {
      return response()->json(
        [
          "msg" => $validator->errors(),
        ],
        $validator->statusCode(),
      );
    }

    $survey = Survey::find($surveyId);
    if ($survey->respondent_id === $newRespondentId) {
      return response()->json([ 'msg' => 'already matching' ], Response::HTTP_OK);
    }
    
    // Check if this form assigns respondent condition tags and bail if so
    $assignsRespondentConditionTags = DB::table('question_assign_condition_tag as qact')
    ->leftJoin('assign_condition_tag as act', 'qact.assign_condition_tag_id', '=', 'act.id')
    ->where('act.scope', 'respondent')
    ->whereRaw(
      'qact.question_id in (
        select q.id from question q where q.question_group_id in (
          select question_group_id from section_question_group
          where section_id in (
            select section_id from form_section where form_id = ?
          )
        )
      )', [ $survey->form_id ])
    ->exists();

    if ($assignsRespondentConditionTags) {
      return response()->json([ 
        'msg' => 'This form assigns respondent condition tags and cannot be transferred'
       ], Response::HTTP_BAD_REQUEST);
    }

    DB::transaction(function () use ($surveyId, $newRespondentId, $survey) {
      $surveyEdgeIds = DB::table('datum')
        ->select('edge_id')
        ->where('survey_id', $surveyId)
        ->whereNotNull('edge_id')
        ->get();
      $respondentGeoIds = DB::table('datum')
        ->select('respondent_geo_id')
        ->where('survey_id', $surveyId)
        ->whereNotNull('respondent_geo_id')
        ->get();
      $respondentNameIds = DB::table('datum')
        ->select('respondent_name_id')
        ->where('survey_id', $surveyId)
        ->whereNotNull('respondent_name_id')
        ->get();

      if ($surveyEdgeIds->count() > 0) {
        DB::table('edge')
        ->whereIn('id',  $surveyEdgeIds->pluck('edge_id'))
        ->update(['source_respondent_id' => $newRespondentId]);
      }
      if ($respondentGeoIds->count() > 0) {
        DB::table('respondent_geo')
        ->whereIn('id',  $respondentGeoIds->pluck('respondent_geo_id'))
        ->update(['respondent_id' => $newRespondentId]);
      }
      if ($respondentNameIds->count() > 0) {
        DB::table('respondent_name')
        ->whereIn('id',  $respondentNameIds->pluck('respondent_name_id'))
        ->update(['respondent_id' => $newRespondentId]);
      } 

      $survey->respondent_id = $newRespondentId;
      $survey->save();
    });

    return response()->json(
      [
        "msg" => "Survey transferred successfully",
      ],
      Response::HTTP_OK,
    );

  }
}
