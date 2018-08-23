<?php

namespace App\Http\Controllers;

use App\Models\Edge;
use App\Models\EdgeDatum;
use App\Models\Interview;
use App\Models\Respondent;
use App\Models\SelfAdministeredSurvey;
use App\Models\Survey;
use App\Models\User;
use App\Models\Token;
use App\Services\DatumService;
use Carbon\Carbon;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Log;
use Ramsey\Uuid\Uuid;
use Validator;
use DB;
use DateTime;

class InterviewController extends Controller
{
    /**
     * Create a new interview and survey from the specified form, study and respondent
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function submit2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'study_id' => 'required|string|min:36|exists:study,id',
            'form_id' => 'required|string|min:36|exists:form,id',
            'respondent_id' => 'required|string|min:36|exists:respondent,id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $survey = new Survey;
        $interview = new Interview;

        DB::transaction(function () use ($request, $survey, $interview) {
            $surveyId = Uuid::uuid4();
            $interviewId = Uuid::uuid4();

            // Create new Survey.
            $survey->id = $surveyId;
            $survey->respondent_id = $request->input('respondent_id');
            $survey->form_id = $request->input('form_id');
            $survey->study_id = $request->input('study_id');
            $survey->save();

            // Create new Interview
            $interview->id = $interviewId;
            $interview->survey_id = $surveyId;
            $interview->user_id = $request->user()->id;
            $interview->start_time = date_create('now');
            $interview->end_time = null;
            $interview->save();

        });

        return response()->json([
            'survey-id' => $survey->id,
            'interview-id' => $interview->id
        ], Response::HTTP_OK);
    }

    public function completeInterview ($interviewId) {
        $validator = Validator::make([
            'interview' => $interviewId
        ], [
            'interview' => 'required|string|min:36|exists:interview,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors()
            ], $validator->statusCode());
        }

        $interview = Interview::find($interviewId);

        $interview->end_time = Carbon::now();
        $interview->save();

        return response()->json([
            'interview' => $interview
        ], Response::HTTP_NO_CONTENT);
    }

    public function submit(Request $request, DatumService $datumService) {

        $validator = Validator::make($request->all(), [
            'study_id' => 'required|string|min:36|exists:study,id',
            'form_id' => 'required|string|min:36|exists:form,id',
            'respondent_id' => 'required|string|min:36|exists:respondent,id',
            'questions' => 'required|array'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $surveyModel = new Survey;
        $interviewModel = new Interview;

        DB::transaction(function() use ($request, $surveyModel, $interviewModel, $datumService) {

            $surveyId = Uuid::uuid4();
            $interviewId = Uuid::uuid4();
            $now = new DateTime();

            // Create new Survey.
            $surveyModel->id = $surveyId;
            $surveyModel->respondent_id = $request->input('respondent_id');
            $surveyModel->form_id = $request->input('form_id');
            $surveyModel->study_id = $request->input('study_id');
            $surveyModel->save();

            // Create new Interview
            $interviewModel->id = $interviewId;
            $interviewModel->survey_id = $surveyId;
            // TODO: start_time and end_time
            $interviewModel->start_time = $now->getTimestamp();
            $interviewModel->end_time = $now->getTimestamp();

            // Iterate through questions and add edges / datum
            foreach($request->input('questions') as $question) {
                if ($question['question_type']['name'] == 'relationship') {

                    $datumModel = $datumService->createDatum ($surveyId, $question['id'], $question['var_name'], '1');

                    foreach($question['selectedRespondents'] as $respondent) {
                        if ($respondent) {
                            // Create edge
                            $edgeId = Uuid::uuid4();
                            $edgeModel = new Edge;
                            $edgeModel->id = $edgeId;
                            $edgeModel->source_respondent_id = $request->input('respondent_id');
                            $edgeModel->target_respondent_id = $respondent['id'];
                            $edgeModel->save();

                            // Create edge_datum
                            $edgeDatumId = Uuid::uuid4();
                            $edgeDatumModel = new EdgeDatum;
                            $edgeDatumModel->id = $edgeDatumId;
                            $edgeDatumModel->edge_id = $edgeId;
                            $edgeDatumModel->datum_id = $datumModel->id;
                            $edgeDatumModel->save();
                        }
                    }
                } else {
                    $datumService->createDatum ($surveyId, $question['id'], $question['var_name'], $question['answer']);
                }
            }
        });

        return response()->json([
        ], Response::HTTP_OK);
    }

    /**
     * Complete the interview by setting the end_time
     * @param $interviewId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function endInterview($interviewId){

        $validator = Validator::make([
            'interviewId' => $interviewId
        ], [
            'interviewId' => 'required|string|min:32|exists:interview,id'
        ]);

        if($validator->fails()){
            return response()->json([
                'msg' => "Invalid interview id",
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $interview = Interview::find($interviewId);
        $interview->end_time = date_create("now");
        $interview->save();

        return response()->json([
            'completed_at' => $interview->end_time
        ], Response::HTTP_OK);
    }

    public function selfAdministeredLogin(Request $request, $formId)
    {
        $validator = Validator::make(array_merge($request->all(), [
            'formId' => $formId
        ]), [
            'formId' => 'required|string|min:36|exists:form,id',
            'respondentAssignedId' => 'required|string|exists:respondent,assigned_id',
            'password' => 'required|string'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $respondentAssignedId = $request->input('respondentAssignedId');
        // TODO: handle respondents with the same assigned ID
        $respondentModel = Respondent::where('assigned_id', $respondentAssignedId)->first();

        $surveyModel = Survey::where('respondent_id', $respondentModel->id)
            ->where('form_id', $formId)->first();

        if ($surveyModel === null) {
            return response()->json([
                'msg' => 'No survey found'
            ], Response::HTTP_NOT_FOUND);
        }

        $sasModel = SelfAdministeredSurvey::where('survey_id', $surveyModel->id)->first();

        if ($sasModel === null) {
            return response()->json([
                'msg' => 'No self-administered survey found'
            ], Response::HTTP_NOT_FOUND);
        }

        $password = $request->input('password');
        if ($sasModel->password !== $password) {
            return response()->json([
                'msg' => 'Incorrect password provided'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $interviewId = Uuid::uuid4();
        $interviewModel = new Interview;
        $interviewModel->id = $interviewId;
        $interviewModel->survey_id = $surveyModel->id;
        $interviewModel->start_time = date('Y-m-d H:i:s');
        $interviewModel->save();

        return response()->json([
            'interviewId' => $interviewId
        ], Response::HTTP_OK);
    }

    public function getInterviewPage(Request $request, $studyId){

        $validator = $this->getRequestValidator($request, $studyId);
        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $q = $this->makePageQuery($request, $studyId);

//        $earliestDate = date_parse($request->get("earliest-date"));
//        $latestDate = date_parse($request->get("latest-date"));
//        $limit = $request->get("limit");
//        $offset = $request->get("offset") ?: 0;

//        if($limit != null || $latestDate == null || $earliestDate == null){
//            $q = $q->take($limit ?: 100);
//        }
        $q = $q->limit(700);
//        $q = $q->offset($offset);
        $interviews = $q->get();


        return response()->json([
            "interviews" => $interviews
        ], Response::HTTP_OK);

    }

    public function getInterviewCount(Request $request, $studyId){

        $validator = $this->getRequestValidator($request, $studyId);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $q = $this->makePageQuery($request, $studyId);

        return response()->json([
            "count" => $q->count()
        ], Response::HTTP_OK);

    }

    private function getRequestValidator(Request $request, $studyId){

        return Validator::make(array_merge($request->all(), ["study_id"=>$studyId]), [
            'study_id' => 'required|string|min:36|exists:study,id',
            'limit' => 'nullable|integer|min:0|max:200',
            'offset' => 'nullable|integer|min:0',
            'latest-date' => 'nullable|date',
            'earliestDate' => 'nullable|date'
        ]);

    }

    /**
     * Get a page of interviews matching all of the parameters provided
     * @param Request $request
     * @return $this|\Illuminate\Database\Eloquent\Builder|static
     */
    private function makePageQuery(Request $request, $studyId){

        $earliestDate = date_parse($request->get("earliest-date"));
        $latestDate = date_parse($request->get("latest-date"));

        $q = Interview::with("survey")
            ->select("*", DB::raw("(select count(*) from datum where survey_id = interview.survey_id) as survey_datum_count"))
            ->with("user");
//        $q = Survey::with('interviews')
//            ->with("form")
//            ->with("dataCount")
//            ->with("respondent");

        if($earliestDate != null) {
//            $q = $q->whereDate("interview.start_time", ">=", $earliestDate);
            $q = $q->whereDay("interview.start_time", ">=", $earliestDate["day"]);
            $q = $q->whereMonth("interview.start_time", ">=", $earliestDate["month"]);
            $q = $q->whereYear("interview.start_time", ">=", $earliestDate["year"]);
        }
//        if($latestDate != null){
//            $q = $q->whereDate("interview.start_time", "<=", $latestDate);
//        }
        $q = $q->orderBy("start_time", "desc");

        return $q;

    }


    public function getInterview ($interviewId) {
        $validator = Validator::make([
            'interview_id' => $interviewId
        ], [
            'interview_id' => 'required|string|min:36|exists:interview,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $interview = Interview::where('id', $interviewId)->with('survey')->first();

        return response()->json([
            'interview' => $interview
        ], Response::HTTP_OK);
    }

    /**
     * Create new interview for the specified survey
     * @param {String} $surveyId
     */
    public function createInterview (Request $request, $surveyId) {
        $validator = Validator::make(array_merge($request->all(), [
            'survey' => $surveyId
        ]), [
            'survey' => 'required|string|min:36|exists:survey,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'altitude' => 'nullable|numeric',
            'accuracy' => 'nullable|numeric'
        ]);

        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $altitude = $request->get('altitude');
        $accuracy = $request->get('accuracy');

        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors()
            ], $validator->statusCode());
        }

        $survey = Survey::find($surveyId);
        if ($survey->completed_at) {
            return response()->json([
                'msg' => "Can't create a new interview for a completed survey"
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        $user = $request->user();
        $interview = Interview::create([
            'id' => Uuid::uuid4(),
            'survey_id' => $surveyId,
            'user_id' => $user->id,
            'start_time' => Carbon::now(),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'altitude' => $altitude,
        ]);

        return response()->json([
            'interview' => $interview
        ], Response::HTTP_OK);

    }
}
