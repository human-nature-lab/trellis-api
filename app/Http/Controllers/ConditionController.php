<?php

namespace App\Http\Controllers;

use App\Models\AssignConditionTag;
use App\Models\ConditionTag;
use App\Models\QuestionAssignConditionTag;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Validator;
use DB;

class ConditionController extends Controller
{

	public function createCondition(Request $request) {

		$validator = Validator::make(array_merge($request->all(), [
		]), [
			'tag' => 'required|string|min:1',
			'logic' => 'required|string|min:1',
			'questions.*.id' => 'string|min:36|exists:question,id'
		]);


		if ($validator->fails() === true) {
			return response()->json([
				'msg' => 'Validation failed',
				'err' => $validator->errors()
			], $validator->statusCode());
		};

		$newAssignConditionTagModel = new AssignConditionTag;

		DB::transaction(function() use ($request, $newAssignConditionTagModel) {

			$conditionTagId = Uuid::uuid4();
			$assignConditionTagId = Uuid::uuid4();

			$newConditionTagModel = new ConditionTag;

			$newConditionTagModel->id = $conditionTagId;
			$newConditionTagModel->name = $request->input('tag');
			$newConditionTagModel->save();

			$newAssignConditionTagModel->id = $assignConditionTagId;
			$newAssignConditionTagModel->condition_tag_id = $conditionTagId;
			$newAssignConditionTagModel->logic = $request->input('logic');
			$newAssignConditionTagModel->scope = $request->input('scope');

			$newAssignConditionTagModel->save();

			foreach($request->input('questions') as $question) {
				$questionAssignConditionTagId = Uuid::uuid4();

				$newQuestionAssignConditionTagModel = new QuestionAssignConditionTag;

				$newQuestionAssignConditionTagModel->id = $questionAssignConditionTagId;
				$newQuestionAssignConditionTagModel->question_id = $question['id'];
				$newQuestionAssignConditionTagModel->assign_condition_tag_id = $assignConditionTagId;
				$newQuestionAssignConditionTagModel->save();
			}
		});

		if ($newAssignConditionTagModel === null) {
			return response()->json([
				'msg' => 'Condition creation failed.'
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		};

		return response()->json([
			'condition' => $newAssignConditionTagModel
		], Response::HTTP_OK);
	}

	public function getAllConditions(Request $request) {

		$conditionTagModel = ConditionTag::select('qact.question_id', 'condition_tag.id', 'condition_tag.name', 'act.logic')
			->join('assign_condition_tag AS act', 'act.condition_tag_id', '=', 'condition_tag.id')
			->join('question_assign_condition_tag AS qact', 'qact.assign_condition_tag_id', '=', 'act.id')
			->get();

		return response()->json([
			'conditions' => $conditionTagModel
		], Response::HTTP_OK);
	}

	public function getAllUniqueConditions(Request $request) {

		$conditionTagModel = ConditionTag::get();

		return response()->json([
			'conditions' => $conditionTagModel
		], Response::HTTP_OK);
	}
}
