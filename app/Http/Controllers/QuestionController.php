<?php

namespace App\Http\Controllers;

use App\Models\Choice;
use App\Models\Parameter;
use App\Models\Question;
use App\Models\QuestionParameter;
use App\Models\TranslationText;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Ramsey\Uuid\Uuid;
use Validator;
use DB;
use Log;

class QuestionController extends Controller
{

    public function moveQuestion(Request $request, $question_id, $question_group_id) {
        $validator = Validator::make(array_merge($request->all(),[
            'question_id' => $question_id,
            'question_group_id' => $question_group_id
        ]), [
            'question_id' => 'required|string|min:36',
            'question_group_id' => 'required|string|min:36',
            'sort_order' => 'required|integer|min:1'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $questionModel = Question::find($question_id);

        if ($questionModel === null) {
            return response()->json([
                'msg' => 'URL resource not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $sortOrder = $request->input('sort_order');

        // increment the sort_order of all questions in the question group with sort order >= sort_order
        DB::statement('update question set sort_order = sort_order + 1 where sort_order >= ? and question_group_Id = ? and deleted_at = null', [$sortOrder, $question_group_id]);

        // This is a comment
        $questionModel->question_group_id = $question_group_id;
        $questionModel->sort_order = $sortOrder;
        $questionModel->save();

        return response()->json([
            'msg' => Response::$statusTexts[Response::HTTP_OK]
        ], Response::HTTP_OK);
    }

	public function getQuestion(Request $request, $id) {

		$validator = Validator::make(
			['id' => $id],
			['id' => 'required|string|min:36|exists:question,id']
		);

		if ($validator->fails() === true) {
			return response()->json([
				'msg' => 'Validation failed',
				'err' => $validator->errors()
			], $validator->statusCode());
		}

		$questionModel = Question::find($id);

		if ($questionModel === null) {
			return response()->json([
				'msg' => 'URL resource not found'
			], Response::HTTP_OK);
		}

		return response()->json([
			'question' => $questionModel
		], Response::HTTP_OK);
	}

	public function getAllQuestions(Request $request, $formId, $localeId) {
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

		$questionModel = Question::select('question.id', 'qt.name AS type', 'question_type_id AS type_id', 'tt.translated_text AS text', 'question.question_group_id', 'question.sort_order', 'var_name AS var')
			->join('question_type AS qt', 'qt.id', '=', 'question.question_type_id')
			->join('translation_text AS tt', 'tt.translation_id', '=', 'question.question_translation_id')
			->join('section_question_group AS sqg', 'sqg.question_group_id', '=', 'question.question_group_id')
			->join('form_section AS fs', 'fs.section_id', '=', 'sqg.section_id')
			->where('fs.form_id', $formId)
			->where('tt.locale_id', $localeId)
			->orderBy('question.sort_order', 'asc')
			->get();

		foreach ($questionModel as $question) {
			switch ($question->type) {
				case "decimal":
					$numericMinModel = QuestionParameter::select('question_parameter.val')
						->join('parameter AS p', 'p.id', '=', 'question_parameter.parameter_id')
						->where('p.name', 'min')
						->where('question_parameter.question_id', $question->id)
						->first();
					$numericMaxModel = QuestionParameter::select('question_parameter.val')
						->join('parameter AS p', 'p.id', '=', 'question_parameter.parameter_id')
						->where('p.name', 'max')
						->where('question_parameter.question_id', $question->id)
						->first();
					$question->min = $numericMinModel != null ? floatval($numericMinModel->val) : $numericMinModel;
					$question->max = $numericMaxModel != null ? floatval($numericMaxModel->val) : $numericMaxModel;
					break;
				case "integer":
					$numericMinModel = QuestionParameter::select('question_parameter.val')
						->join('parameter AS p', 'p.id', '=', 'question_parameter.parameter_id')
						->where('p.name', 'min')
						->where('question_parameter.question_id', $question->id)
						->first();
					$numericMaxModel = QuestionParameter::select('question_parameter.val')
						->join('parameter AS p', 'p.id', '=', 'question_parameter.parameter_id')
						->where('p.name', 'max')
						->where('question_parameter.question_id', $question->id)
						->first();
					$question->min = $numericMinModel != null ? intval($numericMinModel->val) : $numericMinModel;
					$question->max = $numericMaxModel != null ? intval($numericMaxModel->val) : $numericMaxModel;
					break;
				case "year":
				case "time":
				case "year_month":
				case "year":
				case "year_month_day":
				case "year_month_day_time":
					$dateTimeMinModel = QuestionParameter::select('question_parameter.val')
						->join('parameter AS p', 'p.id', '=', 'question_parameter.parameter_id')
						->where('p.name', 'min')
						->where('question_parameter.question_id', $question->id)
						->first();
					$dateTimeMaxModel = QuestionParameter::select('question_parameter.val')
						->join('parameter AS p', 'p.id', '=', 'question_parameter.parameter_id')
						->where('p.name', 'max')
						->where('question_parameter.question_id', $question->id)
						->first();
					$question->date_min = $dateTimeMinModel;
					$question->date_max = $dateTimeMaxModel;
					break;

			}
		}

		return response()->json(
			['questions' => $questionModel],
			Response::HTTP_OK
		);
	}

	public function updateQuestion(Request $request, $questionId) {

		$validator = Validator::make(array_merge($request->all(),[
			'id' => $questionId
		]), [
			'id' => 'required|string|min:36|exists:question,id'
		]);

		if ($validator->fails() === true) {
			return response()->json([
				'msg' => 'Validation failed',
				'err' => $validator->errors()
			], $validator->statusCode());
		}

		$questionModel = Question::find($questionId);

		if ($questionModel === null) {
			return response()->json([
				'msg' => 'URL resource not found'
			], Response::HTTP_NOT_FOUND);
		}

		$questionModel->fill->input();
		$questionModel->save();

		return response()->json([
			'msg' => Response::$statusTexts[Response::HTTP_OK]
		], Response::HTTP_OK);
	}

	public function updateQuestionTypeMultiple(Request $request, $questionId) {
		$validator = Validator::make(array_merge($request->all(),[
					'id' => $questionId
						]),[
					'id' => 'required|string|min:36|exists:question,id',
					'locale_id' => 'required|string|min:36|exists:locale,id'
				]);

		if ($validator->fails() === true) {
			return response()->json([
				'msg' => 'Validation failed',
				'err' => $validator->errors()
			]);
		}

		if ($request->has('question.choices')) {
			$choices = $request->input('question.choices');
			foreach($choices as $choice) {
				$choiceModel = Choice::select('choice.*')
						->join('question_choice AS qc', 'qc.choice_id', '=', 'choice.id')
						->where('qc.id', $choice['id'])
						->first();

				$choiceModel->val = $choice['val'];
				$choiceModel->save();

				$translationTextModel = TranslationText::where('translation_id', $choiceModel->choice_translation_id)
						->where('locale_id', $request->input('locale_id'))
						->first();
				$translationTextModel->translated_text = $choice['text'];
				$translationTextModel->save();
			}
		}
		if ($request->has('question.other')) {
			$otherParameterModel = Parameter::where('name', 'other')
				->first();
			$otherQuestionParameterModel = QuestionParameter::where('question_id', $questionId)
					->where('parameter_id', $otherParameterModel->id)
					->first();
			if ($otherQuestionParameterModel === null) {
				$newOtherQuestionParameterModel = new QuestionParameter;

				$newOtherQuestionParameterModel->id = Uuid::uuid4();
				$newOtherQuestionParameterModel->question_id = $questionId;
				$newOtherQuestionParameterModel->parameter_id = $otherParameterModel->id;
				$newOtherQuestionParameterModel->val = $request->input('question.other');
				$newOtherQuestionParameterModel->save();
			} else {
				$otherQuestionParameterModel->val = $request->input('question.other');
				$otherQuestionParameterModel->save();
			}
		}

		if ($request->has('question.none')) {
			$noneParameterModel = Parameter::where('name', 'none')
				->first();
			$noneQuestionParameterModel = QuestionParameter::where('question_id', $questionId)
					->where('parameter_id', $noneParameterModel->id)
					->first();
			if ($noneQuestionParameterModel === null) {
				$newNoneQuestionParameterModel = new QuestionParameter;

				$newNoneQuestionParameterModel->id = Uuid::uuid4();
				$newNoneQuestionParameterModel->question_id = $questionId;
				$newNoneQuestionParameterModel->parameter_id = $noneParameterModel->id;
				$newNoneQuestionParameterModel->val = $request->input('question.none');
				$newNoneQuestionParameterModel->save();
			} else {
				$noneQuestionParameterModel->val = $request->input('question.none');
				$noneQuestionParameterModel->save();
			}
		}
		return response()->json([

		], Response::HTTP_OK);
	}

	public function removeQuestion(Request $request, $questionId) {

		$validator = Validator::make(
			['id' => $questionId],
			['id' => 'required|string|min:36']
		);

		if ($validator->fails() === true) {
			return response()->json([
				'msg' => 'Validation failed',
				'err' => $validator->errors()
			], $validator->statusCode());
		}

		$questionModel = Form::find($questionId);

		if ($questionModel === null) {
			return response()->json([
				'msg' => 'URL resource was not found'
			], Response::HTTP_NOT_FOUND);
		}
		if ($questionModel->type === 'multiple_select' || $questionModel->type === 'multiple_choice') {
			DB::transaction(function() use($request, $questionId, $questionModel) {
				QuestionParameter::where('question_id', $questionId)->delete();

				Choice::join('question_choice AS qc', 'qc.choice_id', '=', 'choice.id')
					->where('qc.question_id', $questionId)
					->delete();

				QuestionChoice::where('question_id', $questionId)
					->delete();

				QuestionParameter::where('question_id', $questionId)
					->delete();

				$questionModel->delete();
			});
		}
		return response()->json([

		]);
	}

	public function createQuestion(Request $request, $questionGroupId) {

		$validator = Validator::make(array_merge($request->all(),[
				'id' => $questionGroupId
		]), [
				'id' => 'required|string|min:36|exists:question_group,id',
				'translated_text' => 'required|string|min:1',
				'var_name' => 'required|string|min:1',
				'question_type_id' => 'required|string|min:36|exists:question_type,id'
		]);

		if ($validator->fails() === true) {
			return response()->json([
				'msg' => 'Validation failed',
				'err' => $validator->errors()
			], $validator->statusCode());
		}

		$newQuestionModel = new Question;

		DB::transaction(function() use ($request, $newQuestionModel, $questionGroupId) {

			$questionId = Uuid::uuid4();
			$translationId = Uuid::uuid4();
			$translationTextId = Uuid::uuid4();

			$newTranslationModel = new Translation;

			$newTranslationModel->id = $translationId;
			$newTranslationModel->save();

			$newTranslationTextModel = new TranslationText;

			$newTranslationTextModel->id = $translationTextId;
			$newTranslationTextModel->translation_id = $translationId;
			$newTranslationTextModel->locale_id = $request->input('locale_id');
			$newTranslationTextModel->translated_text = $request->input('translated_text');
			$newTranslationTextModel->save();

			$newQuestionModel->id = $questionId;
			$newQuestionModel->question_type_id = $request->input('question_type_id');
			$newQuestionModel->question_translation_id = $translationId;
			$newQuestionModel->question_group_id = $questionGroupId;
			//$newQuestionModel->sort_order = $request->input('sort_order');
            $maxSortOrder = DB::table('question')
                ->where('question_group_id', '=', $questionGroupId)
                ->whereNull('deleted_at')
                ->max('sort_order');

            $newQuestionModel->sort_order = $maxSortOrder + 1;
			$newQuestionModel->var_name = $request->input('var_name');
			$newQuestionModel->save();
			$newQuestionModel->translated_text = $request->input('translated_text');
		});

		if ($newQuestionModel === null) {
			return response()->json([
				'msg' => 'Form creation failed.'
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}

		return response()->json([
			'question' => $newQuestionModel
		], Response::HTTP_OK);
	}
}
