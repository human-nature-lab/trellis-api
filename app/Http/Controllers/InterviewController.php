<?php

namespace App\Http\Controllers;

use App\Models\Edge;
use App\Models\EdgeDatum;
use App\Models\Datum;
use App\Models\Interview;
use App\Models\Respondent;
use App\Models\SelfAdministeredSurvey;
use App\Models\Survey;
use App\Models\User;
use App\Models\Token;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
    public function submit(Request $request)
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

}
