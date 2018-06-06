<?php namespace App\Http\Controllers;

use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
            ->where('study_id', $studyId);

	    Log::debug($q->toSql());

	    return response()->json([
	        'surveys' => $q->get()
        ], Response::HTTP_OK);
    }

	public function getStudySurveys (Request $request, $studyId) {
	    $validator = Validator::make(array_merge($request->all(), [
	        'studyId' => $studyId
        ]), [
            'respondent_id' => 'string|min:32|exists:respondent,id',
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
	public function create($formId)
	{
		$validator = Validator::make([
		    'formId' => $formId
        ], [
            'formId' => 'required|string|min:36|exists:form,id'
        ]);

		if($validator->fails()){
		    return response()->json([
		        'msg' => "Invalid formId",
		        'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $survey = new Survey();
		$survey->id = Uuid::uuid4();
	}

	/**
	 * Display the specified resource.
	 * GET /survey/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /survey/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /survey/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /survey/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}