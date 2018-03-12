<?php namespace App\Http\Controllers;

use App\Models\Survey;
use Illuminate\Http\Response;
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