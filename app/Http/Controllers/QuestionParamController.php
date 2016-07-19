<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Parameter;
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

class QuestionParamController extends Controller
{
	public function updateQuestionNumeric(Request $request, $questionId) {

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
		$minParameterModel = Parameter::where('name', 'min')
			->first();
		$maxParameterModel = Parameter::where('name', 'max')
			->first();

		if ($minParameterModel === null || $maxParameterModel === null) {
			return response()->json([
					'msg' => 'An error has occurred.'
			], Response::HTTP_NOT_FOUND);
		}

		if ($request->has('min')) {
			$minQuestionParameterModel = QuestionParameter::where('parameter_id', $minParameterModel->id)
				->where('question_id', $questionId)
				->first();

			if ($minQuestionParameterModel === null) {
				$newQuestionParameterModel = new QuestionParameter;

				$newQuestionParameterModel->id = Uuid::uuid4();
				$newQuestionParameterModel->question_id = $questionId;
				$newQuestionParameterModel->parameter_id = $minParameterModel->id;
				$newQuestionParameterModel->val = $request->input('min');
				$newQuestionParameterModel->save();
			} else {
				$minQuestionParameterModel->val = $request->input('min');
				$minQuestionParameterModel->save();
			}
		}

		if ($request->has('max')) {
			$maxQuestionParameterModel = QuestionParameter::where('parameter_id', $maxParameterModel->id)
				->where('question_id', $questionId)
				->first();

			if ($maxQuestionParameterModel === null) {
				$newQuestionParameterModel = new QuestionParameter;

				$newQuestionParameterModel->id = Uuid::uuid4();
				$newQuestionParameterModel->question_id = $questionId;
				$newQuestionParameterModel->parameter_id = $maxParameterModel->id;
				$newQuestionParameterModel->val = $request->input('max');
				$newQuestionParameterModel->save();
			} else {
				$maxQuestionParameterModel->val = $request->input('max');
				$maxQuestionParameterModel->save();
			}

		}

		return response()->json([
			'msg' => Response::$statusTexts[Response::HTTP_OK]
		], Response::HTTP_OK);
	}

	public function updateQuestionDateTime(Request $request, $questionId) {

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
		$minParameterModel = Parameter::where('name', 'min')
			->first();
		$maxParameterModel = Parameter::where('name', 'max')
			->first();

		if ($minParameterModel === null || $maxParameterModel === null) {
			return response()->json([
				'msg' => 'An error has occurred.'
			], Response::HTTP_NOT_FOUND);
		}

		if ($request->has('min')) {
			$minQuestionParameterModel = QuestionParameter::where('parameter_id', $minParameterModel->id)
				->where('question_id', $questionId)
				->first();

			if ($minQuestionParameterModel === null) {
				$newQuestionParameterModel = new QuestionParameter;

				$newQuestionParameterModel->id = Uuid::uuid4();
				$newQuestionParameterModel->question_id = $questionId;
				$newQuestionParameterModel->parameter_id = $minParameterModel->id;
				$newQuestionParameterModel->val = $request->input('min');
				$newQuestionParameterModel->save();
			} else {
				$minQuestionParameterModel->val = $request->input('min');
				$minQuestionParameterModel->save();
			}
		}

		if ($request->has('max')) {
			$maxQuestionParameterModel = QuestionParameter::where('parameter_id', $maxParameterModel->id)
				->where('question_id', $questionId)
				->first();

			if ($maxQuestionParameterModel === null) {
				$newQuestionParameterModel = new QuestionParameter;

				$newQuestionParameterModel->id = Uuid::uuid4();
				$newQuestionParameterModel->question_id = $questionId;
				$newQuestionParameterModel->parameter_id = $maxParameterModel->id;
				$newQuestionParameterModel->val = $request->input('max');
				$newQuestionParameterModel->save();
			} else {
				$maxQuestionParameterModel->val = $request->input('max');
				$maxQuestionParameterModel->save();
			}

		}

		return response()->json([
			'msg' => Response::$statusTexts[Response::HTTP_OK]
		], Response::HTTP_OK);
	}
}
