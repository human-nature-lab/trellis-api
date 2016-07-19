<?php

namespace App\Http\Controllers;

use App\Models\AssignSkipTag;
use App\Models\ConditionTag;
use App\Models\QuestionGroupSkip;
use App\Models\Skip;
use App\Models\SkipConditionTag;
use App\Models\SkipTag;
use App\Models\QuestionAssignSkipTag;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Validator;
use DB;

class SkipController extends Controller
{

	public function createQuestionGroupSkip(Request $request) {

		$validator = Validator::make(array_merge($request->all(), [
		]), [
			'show_hide' => 'required|boolean',
			'any_all' => 'required|boolean',
			'question_group_id' => 'required|string|min:36|exists:question_group,id',
			'conditions.*.id' => 'string|min:36|exists:condition_tag,id'
		]);


		if ($validator->fails() === true) {
			return response()->json([
				'msg' => 'Validation failed',
				'err' => $validator->errors()
			], $validator->statusCode());
		};

//		$skipPrecedenceModel = Skip::select('skip.precedence');

		$newSkipModel = new Skip;

		DB::transaction(function() use ($request, $newSkipModel) {

			$newSkipModelId = Uuid::uuid4();

			$newSkipModel->id = $newSkipModelId;
			$newSkipModel->show_hide = $request->input('show_hide');
			$newSkipModel->any_all = $request->input('any_all');
			$newSkipModel->precedence = $request->input('precedence');
			$newSkipModel->save();

			$newQuestionGroupSkip = new QuestionGroupSkip;

			$newQuestionGroupSkip->id = Uuid::uuid4();
			$newQuestionGroupSkip->question_group_id = $request->input('question_group_id');
			$newQuestionGroupSkip->skip_id = $newSkipModelId;
			$newQuestionGroupSkip->save();

			foreach($request->input('conditions') as $condition) {

				$newSkipConditionTag = new SkipConditionTag;

				$newSkipConditionTag->id = Uuid::uuid4();
				$newSkipConditionTag->skip_id = $newSkipModelId;
				$newSkipConditionTag->condition_tag_id = $condition['id'];
				$newSkipConditionTag->save();
			}
		});

		if ($newSkipModel === null) {
			return response()->json([
				'msg' => 'Skip creation failed.'
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		};

		return response()->json([
			'skip' => $newSkipModel
		], Response::HTTP_OK);
	}

	public function getAllQuestionGroupSkips(Request $request) {

		$skipModel = Skip::select('skip.id', 'skip.show_hide', 'skip.any_all', 'skip.precedence', 'qgs.question_group_id')
			->join('question_group_skip AS qgs', 'qgs.skip_id', '=', 'skip.id')
			->get();

		if ($skipModel === null) {
			return response()->json([
				'msg' => 'No skips found.'
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}

		foreach($skipModel as $skip) {
			$skipConditionModel = ConditionTag::select('condition_tag.name', 'condition_tag.id')
				->join('skip_condition_tag AS sct', 'sct.condition_tag_id', '=', 'condition_tag.id')
				->where('sct.skip_id', $skip->id)
				->get();

			if ($skipConditionModel !== null) {
				$conditions = array();
				foreach ($skipConditionModel as $skipCondition) {
					array_push($conditions, $skipCondition);
				}
				$skip->conditions = $conditions;
			}
		}

		return response()->json([
			'skips' => $skipModel
		], Response::HTTP_OK);
	}
}
