<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Validator;
use DB;
use App\Models\GroupTagType;

class GroupTagTypeController extends Controller
{
    public function getAllGroupTagTypes(Request $request)
    {
        $groupTagTypes = GroupTagType::get();

        return response()->json(
            ['group_tag_types' => $groupTagTypes],
            Response::HTTP_OK
        );
    }

    public function removeGroupTagType(Request $request, $id)
    {
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

        $groupTagType = GroupTagType::find($id);

        if ($groupTagType === null) {
            return response()->json([
                'msg' => 'URL resource was not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $groupTagType->delete();

        return response()->json([

        ]);
    }

    public function createGroupTagType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|min:1|max:255'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $id = Uuid::uuid4();
        $groupTagTypeName = $request->input('name');

        $newGroupTagTypeModel = new GroupTagType;
        $newGroupTagTypeModel->id = $id;
        $newGroupTagTypeModel->name = $groupTagTypeName;
        $newGroupTagTypeModel->save();

        return response()->json([
            'group_tag_type' => $newGroupTagTypeModel
        ], Response::HTTP_OK);
    }
}
