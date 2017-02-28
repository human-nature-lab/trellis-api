<?php

namespace App\Http\Controllers;

use App\Models\Edge;
use App\Models\EdgeDatum;
use App\Models\Datum;
use App\Models\Interview;
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

	public function submit(Request $request) {

		$validator = Validator::make($request->all(), [
			'study_id' => 'required|string|min:36|exists:study,id',
            'form_id' => 'required|string|min:36|exists:form,id',
            'respondent_id' => 'required|string|min:36|exists:respondent,id',
            'token_id' => 'required|string|min:36|exists:token,id',
			'questions' => 'required|array'
		]);

		if ($validator->fails() === true) {
			return response()->json([
				'msg' => 'Validation failed',
				'err' => $validator->errors()
			], $validator->statusCode());
		}

		$token = Token::find($request->input('token_id'));
        $user = User::find($token->user_id);
        $surveyModel = new Survey;
		$interviewModel = new Interview;

		DB::transaction(function() use ($request, $surveyModel, $interviewModel, $user) {

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
            $interviewModel->user_id = $user->id;
            // TODO: start_time and end_time
            $interviewModel->start_time = $now->getTimestamp();
            $interviewModel->end_time = $now->getTimestamp();

            // Iterate through questions and add edges / datum
            foreach($request->input('questions') as $question) {
                if ($question['question_type']['name'] == 'relationship') {
                    // Create new datum
                    $datumId = Uuid::uuid4();
                    $datumModel = new Datum;
                    $datumModel->id = $datumId;
                    $datumModel->survey_id = $surveyId;
                    //TODO deal with choice_id
                    $datumModel->name = $question['var_name'];
                    $datumModel->val = "1";
                    $datumModel->question_id = $question['id'];
                    $datumModel->save();

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
                            $edgeDatumModel->datum_id = $datumId;
                            $edgeDatumModel->save();
                        }
                    }
                } else {
                    // Create new datum
                    $datumId = Uuid::uuid4();
                    $datumModel = new Datum;
                    $datumModel->id = $datumId;
                    $datumModel->survey_id = $surveyId;
                    //TODO deal with choice_id
                    $datumModel->name = $question['var_name'];
                    $datumModel->val = $question['answer'];
                    $datumModel->question_id = $question['id'];
                    $datumModel->save();
                }
            }
		});

		return response()->json([
		], Response::HTTP_OK);
	}

}
