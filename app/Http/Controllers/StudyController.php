<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Validator;
use App\Models\Study;

class StudyController extends Controller
{

	public function getStudy(Request $request, $id) {

		$validator = Validator::make(
			['id' => $id],
			['id' => 'required|string|min:36']
		);

		if ($validator->fails() === true) {
			return response()->json([
				'msg' => 'Validation failed',
				'err' => $validator->errors()
			], $validator->statusCode());
		}

		$studyModel = Study::find($id);

		if ($studyModel === null) {
			return response()->json([
				'msg' => 'URL resource not found'
			], Response::HTTP_OK);
		}

		return response()->json([
			'study' => $studyModel
		], Response::HTTP_OK);
	}

	public function getAllStudies(Request $request) {

		$studyModel = Study::select('study.id', 'study.name', 'study.photo_quality', 'l.language_name', 'study.default_locale_id')
			->join('locale AS l', 'l.id', '=', 'default_locale_id')
			->get();

		return response()->json(
			['studies' => $studyModel],
			Response::HTTP_OK
		);
	}

	public function updateStudy(Request $request, $id) {

		$validator = Validator::make(array_merge($request->all(),[
			'id' => $id
		]), [
			'id' => 'required|string|min:36|exists:study,id',
			'name' => 'string|min:1',
			'photo_quality' => 'required|integer|between:1,100',
			'census_form_master_id' => 'string|min:1',
			'default_locale_id' => 'required|string|min:36|exists:locale,id'
		]);

		if ($validator->fails() === true) {
			return response()->json([
				'msg' => 'Validation failed',
				'err' => $validator->errors()
			], $validator->statusCode());
		}

		$studyModel = Study::find($id);

		if ($studyModel === null) {
			return response()->json([
				'msg' => 'URL resource not found'
			], Response::HTTP_NOT_FOUND);
		}

		$studyModel->fill($request->input());
		$studyModel->save();

		return response()->json([
			'msg' => Response::$statusTexts[Response::HTTP_OK]
		], Response::HTTP_OK);
	}

	public function removeStudy(Request $request, $id) {

		$validator = Validator::make(
			['id' => $id],
			['id' => 'required|string|min:36']
		);

		if ($validator->fails() === true) {
			return response()->json([
				'msg' => 'Validation failed',
				'err' => $validator->errors()
			], $validator->statusCode());
		}

		$studyModel = Study::find($id);

		if ($studyModel === null) {
			return response()->json([
				'msg' => 'URL resource was not found'
			], Response::HTTP_NOT_FOUND);
		}

		$studyModel->delete();

		return response()->json([

		]);
	}

	public function createStudy(Request $request) {

		$validator = Validator::make($request->all(), [
			'name' => 'required|string|min:1',
			'photo_quality' => 'required|integer|between:1,100',
			'census_form_master_id' => 'string|min:1',
			'default_locale_id' => 'required|string|min:1'
		]);

		if ($validator->fails() === true) {
			return response()->json([
				'msg' => 'Validation failed',
				'err' => $validator->errors()
			], $validator->statusCode());
		}

		$studyName = $request->input('name');
		$studyPhotoQuality = $request->input('photo_quality');
		$studyCensusFormMasterId = $request->input('census_form_master_id');
		$studyDefaultLocaleId = $request->input('default_locale_id');

		$newStudyModel = new Study;
		$newStudyModel->id = Uuid::uuid4();
		$newStudyModel->name = $studyName;
		$newStudyModel->photo_quality = $studyPhotoQuality;
		$newStudyModel->census_form_master_id = $studyCensusFormMasterId;
		$newStudyModel->default_locale_id = $studyDefaultLocaleId;
		$newStudyModel->save();

		return response()->json([
			'study' => $newStudyModel
		], Response::HTTP_OK);
	}
}
