<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Validator;
use App\Models\TranslationText;

class TranslationTextController extends Controller
{

	public function getTranslationText(Request $request, $id) {

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

		$translationTextModel = TranslationText::find($id);

		if ($translationTextModel === null) {
			return response()->json([
				'msg' => 'URL resource not found'
			], Response::HTTP_OK);
		}

		return response()->json([
			'translationText' => $translationTextModel
		], Response::HTTP_OK);
	}

	public function getAllTranslationTexts(Request $request) {

		$translationTextModel = Form::get();

		return response()->json(
			['translationTexts' => $translationTextModel],
			Response::HTTP_OK
		);
	}

	public function updateTranslationText(Request $request, $id) {

		$validator = Validator::make(array_merge($request->all(),[
			'id' => $id
		]), [
			'id' => 'required|string|min:36',
			'locale_id' => 'string|min:36',
			'translated_text' => 'required|string|min:1'
		]);

		if ($validator->fails() === true) {
			return response()->json([
				'msg' => 'Validation failed',
				'err' => $validator->errors()
			], $validator->statusCode());
		}

		$translationTextModel = TranslationText::find($id);

		if ($translationTextModel === null) {
			return response()->json([
				'msg' => 'URL resource not found'
			], Response::HTTP_NOT_FOUND);
		}

		$translationTextModel->fill->input();
		$translationTextModel->save();

		return response()->json([
			'msg' => Response::$statusTexts[Response::HTTP_OK]
		], Response::HTTP_OK);
	}

	public function removeTranslationText(Request $request, $id) {

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

		$translationTextModel = TranslationText::find($id);

		if ($translationTextModel === null) {
			return response()->json([
				'msg' => 'URL resource was not found'
			], Response::HTTP_NOT_FOUND);
		}

		$translationTextModel->delete();

		return response()->json([

		], Response::HTTP_NO_CONTENT);
	}

	public function createTranslationText(Request $request) {

		$validator = Validator::make($request->all(), [
			'translation_id' => 'required|string|min:36',
			'locale_id' => 'string|min:36',
			'translated_text' => 'required|string|min:1'
		]);

		if ($validator->fails() === true) {
			return response()->json([
				'msg' => 'Validation failed',
				'err' => $validator->errors()
			], $validator->statusCode());
		}

		$translationTextId = Uuid::uuid4();
		$localeId = $request->input('locale_id');
		$translationId = $request->input('translation_id');
		$translatedText = $request->input('translated_text');

		$newTranslationTextModel = new TranslationText;
		$newTranslationTextModel->id = $translationTextId;
		$newTranslationTextModel->translation_id = $translationId;
		$newTranslationTextModel->locale_id = $localeId;
		$newTranslationTextModel->translated_text = $translatedText;
		$newTranslationTextModel->save();

		return response()->json([
			'translationText' => $newTranslationTextModel
		], Response::HTTP_OK);
	}
}
