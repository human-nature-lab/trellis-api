<?php

namespace App\Http\Controllers;

use App\Models\AssignConditionTag;
use App\Models\ConditionTag;
use App\Models\QuestionAssignConditionTag;
use App\Models\RespondentConditionTag;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Log;
use Ramsey\Uuid\Uuid;
use Validator;
use DB;

class ConditionController extends Controller
{
    public function deleteAssignConditionTag(Request $request, $id)
    {
        $validator = Validator::make(
            ['id' => $id],
            ['id' => 'required|string|min:36|exists:assign_condition_tag']
        );

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        };

        \Log::info('deleteAssignConditionTag, id: ' . $id);

        $assignConditionTagModel = AssignConditionTag::find($id);

        \Log::info('deleteAssignConditionTag, assignConditionTagModel: ' . $assignConditionTagModel);

        if ($assignConditionTagModel === null) {
            return response()->json([
                'msg' => 'Invalid assign_condition_tag id.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        };

        $assignConditionTagModel->delete();

        return response()->json([
            'msg' => 'OK'
        ], Response::HTTP_OK);
    }

    public function editConditionLogic(Request $request)
    {
        $validator = Validator::make(array_merge($request->all(), [
        ]), [
            'logic' => 'required|string|min:1',
            'id' => 'required|string|min:36|exists:assign_condition_tag,id'
        ]);


        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        };

        $assignConditionTagModel = AssignConditionTag::find($request->input('id'));

        if ($assignConditionTagModel === null) {
            return response()->json([
                'msg' => 'Invalid assign_condition_tag id.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        };

        DB::transaction(function () use ($request, $assignConditionTagModel) {
            $logic = $request->input('logic');

            $assignConditionTagModel->logic = $logic;
            $assignConditionTagModel->save();
        });

        return response()->json([
            'assign_condition_tag' => $assignConditionTagModel
        ], Response::HTTP_OK);
    }

    public function editConditionScope(Request $request)
    {
        $validator = Validator::make(array_merge($request->all(), [
        ]), [
            'scope' => 'required|string|min:1',
            'id' => 'string|min:36|exists:assign_condition_tag,id'
        ]);


        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        };

        $assignConditionTagModel = AssignConditionTag::find($request->input('id'));

        if ($assignConditionTagModel === null) {
            return response()->json([
                'msg' => 'Invalid assign_condition_tag id.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        };

        DB::transaction(function () use ($request, $assignConditionTagModel) {
            $scope = $request->input('scope');

            $assignConditionTagModel->scope = $scope;
            $assignConditionTagModel->save();
        });

        return response()->json([
            'assign_condition_tag' => $assignConditionTagModel
        ], Response::HTTP_OK);
    }

    public function createCondition(Request $request)
    {
        $validator = Validator::make(array_merge($request->all(), [
        ]), [
            'tag' => 'required|string|min:1',
        ]);


        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        };

        $condition = new ConditionTag;
        $condition->id = Uuid::uuid4();
        $condition->name = $request->input('tag');
        $condition->save();

        return response()->json([
            'condition' => $condition,
        ], Response::HTTP_OK);
    }

    public function getAllConditions(Request $request)
    {
        $conditionTagModel = ConditionTag::select('qact.question_id', 'condition_tag.id', 'condition_tag.name', 'act.logic')
            ->join('assign_condition_tag AS act', 'act.condition_tag_id', '=', 'condition_tag.id')
            ->join('question_assign_condition_tag AS qact', 'qact.assign_condition_tag_id', '=', 'act.id')
            ->get();

        return response()->json([
            'conditions' => $conditionTagModel
        ], Response::HTTP_OK);
    }

    public function searchAllConditions(Request $request)
    {
        $validator = Validator::make(array_merge($request->all(), [
        ]), [
            'search' => 'required|string|min:1'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        };

        $searchTerm = $request->input('search');
        $conditionNames = ConditionTag::where('name', 'LIKE', '%' . $searchTerm . '%')
            ->select('name as condition_tag_name')
            ->groupBy('name')
            ->get()
            ->toArray();

        return response()->json($conditionNames, Response::HTTP_OK);
    }

    public function getAllUniqueConditions()
    {
        $conditionTagModel = ConditionTag::get();

        return response()->json([
            'conditions' => $conditionTagModel
        ], Response::HTTP_OK);
    }

    public function getAllRespondentConditionTags() {
        $tags = ConditionTag::whereIn('id', function($query) {
            $query
                ->select('condition_tag_id')
                ->from('respondent_condition_tag');
        })->orWhereIn('id', function($query) {
              $query
                  ->select('condition_tag_id')
                  ->from('assign_condition_tag')
                  ->where('scope', '=', 'respondent');
        })->distinct()->get();

        return response()->json([
            'conditions' => $tags
        ], Response::HTTP_OK);
    }
}
