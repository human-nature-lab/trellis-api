<?php

namespace App\Http\Controllers;

use App\Models\AssignSkipTag;
use App\Models\ConditionTag;
use App\Models\QuestionGroup;
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
    public function deleteQuestionGroupSkip(Request $request, $id) {

        $validator = Validator::make(array_merge($request->all(), [
            'id' => $id
        ]), [
            'id' => 'required|string|min:36|exists:skip'
        ]);


        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        };


        $skipModel = Skip::find($id);

        if ($skipModel === null) {
            return response()->json([
                'msg' => 'URL resource was not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $skipModel->delete();

        return response()->json([

        ]);
    }

    public function updateQuestionGroupSkip(Request $request, $skipId) {

        $validator = Validator::make(array_merge($request->all(), [
            'id' => $skipId
        ]), [
            'id' => 'required|string|min:36|exists:skip',
            'show_hide' => 'required|boolean',
            'any_all' => 'required|boolean',
            'precedence' => 'required|integer',
            'conditions.*.condition_tag_name' => 'string|min:1'
        ]);


        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        };


        $skipModel = Skip::find($skipId);

        DB::transaction(function() use ($request, $skipModel, $skipId) {
            $skipModel->show_hide = $request->input('show_hide');
            $skipModel->any_all = $request->input('any_all');
            $skipModel->precedence = $request->input('precedence');
            $skipModel->save();

            $conditions = $request->input('conditions');
            foreach($conditions as $newCondition) {
                $exists = false;
                foreach ($skipModel->conditions as $existingCondition) {
                    if ($newCondition['condition_tag_name'] == $existingCondition->condition_tag_name) {
                        $exists = true;
                    }
                }
                if (! $exists) {
                    // Add new condition
                    $newSkipConditionTag = new SkipConditionTag;

                    $newSkipConditionTag->id = Uuid::uuid4();
                    $newSkipConditionTag->skip_id = $skipId;
                    $newSkipConditionTag->condition_tag_name = $newCondition['condition_tag_name'];
                    $newSkipConditionTag->save();
                }
            }

            foreach($skipModel->conditions as $existingCondition) {
                $exists = false;
                foreach ($conditions as $newCondition) {
                    if ($newCondition['condition_tag_name'] == $existingCondition->condition_tag_name) {
                        $exists = true;
                    }
                }

                if (! $exists) {
                    // Remove deleted conditions
                    SkipConditionTag::where('skip_id', $skipId)
                        ->where('condition_tag_name', $existingCondition->condition_tag_name)
                        ->delete();
                }
            }
        });

        $returnSkipModel = Skip::with('conditions')->find($skipId);

        return response()->json([
            'skip' => $returnSkipModel
        ], Response::HTTP_OK);

    }

	public function createQuestionGroupSkip(Request $request) {

		$validator = Validator::make(array_merge($request->all(), [
		]), [
			'show_hide' => 'required|boolean',
			'any_all' => 'required|boolean',
			'question_group_id' => 'required|string|min:36|exists:question_group,id',
			'conditions.*.condition_tag_name' => 'string|min:1'
		]);


		if ($validator->fails() === true) {
			return response()->json([
				'msg' => 'Validation failed',
				'err' => $validator->errors()
			], $validator->statusCode());
		};

//		$skipPrecedenceModel = Skip::select('skip.precedence');

		$newSkipModel = new Skip;
        $newSkipModelId = Uuid::uuid4();
        $questionGroupId = $request->input('question_group_id');

		DB::transaction(function() use ($request, $newSkipModel, $newSkipModelId, $questionGroupId) {


			$newSkipModel->id = $newSkipModelId;
			$newSkipModel->show_hide = $request->input('show_hide');
			$newSkipModel->any_all = $request->input('any_all');
			$newSkipModel->precedence = $request->input('precedence');
			$newSkipModel->save();

			$newQuestionGroupSkip = new QuestionGroupSkip;

			$newQuestionGroupSkip->id = Uuid::uuid4();
			$newQuestionGroupSkip->question_group_id = $questionGroupId;
			$newQuestionGroupSkip->skip_id = $newSkipModelId;
			$newQuestionGroupSkip->save();

			foreach($request->input('conditions') as $condition) {

				$newSkipConditionTag = new SkipConditionTag;

				$newSkipConditionTag->id = Uuid::uuid4();
				$newSkipConditionTag->skip_id = $newSkipModelId;
				$newSkipConditionTag->condition_tag_name = $condition['condition_tag_name'];
				$newSkipConditionTag->save();
			}
		});

		if ($newSkipModel === null) {
			return response()->json([
				'msg' => 'Skip creation failed.'
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		};

		//$returnSkipModel = Skip::find($newSkipModelId)->with('conditions')->get();
        $returnSkipsModel = QuestionGroup::find($questionGroupId)->skips()->get();

		return response()->json([
			'skips' => $returnSkipsModel
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
