<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Validator;
use DB;
use App\Models\Form;
use App\Models\Study;
use App\Models\StudyForm;
use App\Models\Translation;
use App\Models\TranslationText;

class DeviceController extends Controller
{

	public function getDevice(Request $request, $id) {

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

		$formModel = Form::find($id);

		if ($formModel === null) {
			return response()->json([
				'msg' => 'URL resource not found'
			], Response::HTTP_OK);
		}

		return response()->json([
			'form' => $formModel
		], Response::HTTP_OK);
	}

	public function getAllForms(Request $request) {

		if (!empty($request->input('study_id'))) {
			$formModel = Form::select('form.id', 'form.version', 'tt.translated_text')
					->join('translation_text AS tt', 'tt.translation_id', '=', 'form.name_translation_id')
					->join('study_form AS sf', 'sf.form_master_id', '=', 'form.form_master_id')
					->where('sf.study_id', $request->input('study_id'))
					->get();
		} else {
			$formModel = Form::get();
		}

		return response()->json(
			['forms' => $formModel],
			Response::HTTP_OK
		);
	}

	public function updateForm(Request $request, $id) {

		$validator = Validator::make(array_merge($request->all(),[
			'id' => $id
		]), [
			'id' => 'required|string|min:36',
			'form_master_id' => 'string|min:36',
			'name_translation_id' => 'string|min:36'
		]);

		if ($validator->fails() === true) {
			return response()->json([
				'msg' => 'Validation failed',
				'err' => $validator->errors()
			], $validator->statusCode());
		}

		$formModel = Form::find($id);

		if ($formModel === null) {
			return response()->json([
				'msg' => 'URL resource not found'
			], Response::HTTP_NOT_FOUND);
		}

		$formModel->fill->input();
		$formModel->save();

		return response()->json([
			'msg' => Response::$statusTexts[Response::HTTP_OK]
		], Response::HTTP_OK);
	}

	public function removeForm(Request $request, $id) {

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

		$formModel = Form::find($id);

		if ($formModel === null) {
			return response()->json([
				'msg' => 'URL resource was not found'
			], Response::HTTP_NOT_FOUND);
		}

		$formModel->delete();

		return response()->json([

		]);
	}

	public function createForm(Request $request) {

		$validator = Validator::make($request->all(), [
			'translated_text' => 'required|string|min:1',
			'study_id' => 'required|string|min:36',
			'form_master_id' => 'string|min:36'
		]);

		if ($validator->fails() === true) {
			return response()->json([
				'msg' => 'Validation failed',
				'err' => $validator->errors()
			], $validator->statusCode());
		}

		$studyModel = Study::find($request->input('study_id'));

		$newFormModel = new Form;

		DB::transaction(function() use ($request, $newFormModel, $studyModel) {

			$translationId = Uuid::uuid4();
			$translationTextId = Uuid::uuid4();
			$formId = Uuid::uuid4();
			$studyFormId = Uuid::uuid4();

			// Create new Translation.
			$newTranslationModel = new Translation;
			$newTranslationModel->id = $translationId;
			$newTranslationModel->save();

			// Create new TranslationText.
			$newTranslationTextModel = new TranslationText;
			$newTranslationTextModel->id = $translationTextId;
			$newTranslationTextModel->translation_id = $translationId;
			$newTranslationTextModel->locale_id = $studyModel->default_locale_id;
			$newTranslationTextModel->translated_text = $request->input('translated_text');
			$newTranslationTextModel->save();

			// Set FormMasterId.
			if (empty($request->input('form_master_id'))) {
				$formMasterId = $formId;
			} else {
				$formMasterId = $request->input('form_master_id');
			}

			// Set Version.
			$version = Form::where('form_master_id', '=', $formMasterId)
					->max('version');

			if ($version !== null) {
				$version++;
				$formVersion = $version;
			} else {
				$formVersion = 1;
			}

			// Create new Form.
			$newFormModel->id = $formId;
			$newFormModel->form_master_id = $formMasterId;
			$newFormModel->name_translation_id = $translationId;
			$newFormModel->version = $formVersion;
			$newFormModel->save();

			$newFormModel->translated_text = $request->input('translated_text');

			// Create new StudyForm.
			$newStudyFormModel = new StudyForm;
			$newStudyFormModel->id = $studyFormId;
			$newStudyFormModel->study_id = $request->input('study_id');
			$newStudyFormModel->form_master_id = $formMasterId;
			$newStudyFormModel->sort_order = 0;
			$newStudyFormModel->save();

		});

		if ($newFormModel === null) {
			return response()->json([
				'msg' => 'Form creation failed.'
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}

		return response()->json([
			'form' => $newFormModel
		], Response::HTTP_OK);
	}
}
