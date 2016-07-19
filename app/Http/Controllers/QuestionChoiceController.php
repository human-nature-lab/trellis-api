<?php

namespace App\Http\Controllers;

use App\Models\Parameter;
use App\Models\QuestionParameter;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Validator;
use App\Models\QuestionChoice;
use App\Models\Choice;
use App\Models\Translation;
use App\Models\TranslationText;
use DB;

class QuestionChoiceController extends Controller
{
	public function createQuestionChoice(Request $request, $questionId) {

		$validator = Validator::make(array_merge($request->all(), [
				'question_id' => $questionId]), [
				'question_id' => 'required|string|min:36|exists:question,id',
				'locale_id' => 'required|string|min:36|exists:locale,id'
		]);

		if ($validator->fails() === true) {
			return response()->json([
					'msg' => 'Validation failed',
					'err' => $validator->errors()
			], $validator->statusCode());
		}

		$questionChoiceId = Uuid::uuid4();
		$choiceId = Uuid::uuid4();
		$translationId = Uuid::uuid4();
		$translationTextId = Uuid::uuid4();

		$newQuestionChoiceModel = new QuestionChoice;

		DB::transaction(function() use($request, $questionChoiceId, $choiceId, $translationId, $translationTextId, $newQuestionChoiceModel, $questionId) {

			$newTranslationModel = new Translation;
			$newTranslationModel->id = $translationId;
			$newTranslationModel->save();

			$newTranslationTextModel = new TranslationText;
			$newTranslationTextModel->id = $translationTextId;
			$newTranslationTextModel->translation_id = $translationId;
			$newTranslationTextModel->locale_id = $request->input('locale_id');
			$newTranslationTextModel->translated_text = $request->input('translated_text');
			$newTranslationTextModel->save();

			$newChoiceModel = new Choice;
			$newChoiceModel->id = $choiceId;
			$newChoiceModel->choice_translation_id = $translationId;
			$newChoiceModel->val = $request->input('val');
			$newChoiceModel->save();

			$newQuestionChoiceModel->id = $questionChoiceId;
			$newQuestionChoiceModel->question_id = $questionId;
			$newQuestionChoiceModel->choice_id = $choiceId;
			$newQuestionChoiceModel->sort_order = $request->input('sort_order');
			$newQuestionChoiceModel->save();

			$newQuestionChoiceModel->translated_text = $request->input('translated_text');
			$newQuestionChoiceModel->sort_order = $request->input('sort_order');
			$newQuestionChoiceModel->val = $request->input('val');

		});

		return response()->json([
				'questionChoice' => $newQuestionChoiceModel
		], Response::HTTP_OK);
	}

	public function removeQuestionChoice(Request $request, $id) {

		$validator = Validator::make(
				['id' => $id],
				['id' => 'required|string|min:36|exists:question_choice,id']
		);

		if ($validator->fails() === true) {
			return response()->json([
					'msg' => 'Validation failed',
					'err' => $validator->errors()
			], $validator->statusCode());
		};

		$questionChoiceModel = QuestionChoice::find($id);

		if ($questionChoiceModel === null) {
			return response()->json([
					'msg' => 'URL resource was not found'
			], Response::HTTP_NOT_FOUND);
		};

		DB::transaction(function() use($request, $questionChoiceModel) {
			$choiceModel = Choice::find($questionChoiceModel->choice_id);
			$translationTextModel = TranslationText::where('translation_id', $choiceModel->choice_translation_id);
			$translationModel = Translation::find($choiceModel->choice_translation_id);

			$translationTextModel->delete();
			$translationModel->delete();
			$choiceModel->delete();
			$questionChoiceModel->delete();
		});

		return response()->json([

		]);
	}

	public function getQuestionChoice(Request $request, $id) {

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

		$questionGroupModel = QuestionGroup::find($id);

		if ($questionGroupModel === null) {
			return response()->json([
				'msg' => 'URL resource not found'
			], Response::HTTP_OK);
		}

		return response()->json([
			'questionGroup' => $questionGroupModel
		], Response::HTTP_OK);
	}

	public function getAllQuestionChoices(Request $request, $formId, $localeId) {

		$validator = Validator::make(array_merge($request->all(),[
				'formId' => $formId,
				'localeId' => $localeId
		]), [
				'formId' => 'required|string|min:36|exists:form,id',
				'localeId' => 'required|string|min:36|exists:locale,id'
		]);

		if ($validator->fails() === true) {
			return response()->json([
					'msg' => 'Validation failed',
					'err' => $validator->errors()
			], $validator->statusCode());
		}

		$questionChoiceModel = QuestionChoice::select('question_choice.id', 'question_choice.sort_order', 'question_choice.question_id', 'tt.translated_text AS text', 'c.val')
				->join('choice AS c', 'c.id', '=', 'question_choice.choice_id')
				->join('translation_text AS tt', 'tt.translation_id', '=', 'c.choice_translation_id')
				->join('question AS q', 'q.id', '=', 'question_choice.question_id')
				->join('section_question_group AS sqg', 'sqg.question_group_id', '=', 'q.question_group_id')
				->join('form_section AS fs', 'fs.section_id', '=', 'sqg.section_id')
				->where('fs.form_id', $formId)
				->where('tt.locale_id', $localeId)
				->get();

		return response()->json(
			['questionChoices' => $questionChoiceModel],
			Response::HTTP_OK
		);
	}

	public function updateQuestionChoice(Request $request, $id) {

		$validator = Validator::make(array_merge($request->all(),[
				'id' => $id
		]), [
				'id' => 'required|string|min:36|exists:question_choice,id'
		]);

		if ($validator->fails() === true) {
			return response()->json([
					'msg' => 'Validation failed',
					'err' => $validator->errors()
			], $validator->statusCode());
		}

		$questionChoiceModel = QuestionChoice::find($id);

		if ($questionChoiceModel === null) {
			return response()->json([
					'msg' => 'URL resource not found'
			], Response::HTTP_NOT_FOUND);
		}

		$choiceModel = Choice::find($questionChoiceModel->choice_id);

		if ($choiceModel === null) {
			return response()->json([
				'msg' => 'URL resource not found'
			], Response::HTTP_NOT_FOUND);
		}

		if ($request->input('val') != null) {
			$choiceModel->val = $request->input('val');
			$choiceModel->save();
		}

		if ($request->input('translated_text') != null) {
			$translationModel = TranslationText::where('translation_id', $choiceModel->choice_translation_id)->first();

			$translationModel->translated_text = $request->input('translated_text');
			$translationModel->save();
		}

		return response()->json([
				'msg' => Response::$statusTexts[Response::HTTP_OK]
		], Response::HTTP_OK);
	}
}
