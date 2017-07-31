<?php

namespace App\Http\Controllers;

use App\Models\QuestionType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class QuestionTypeController extends Controller
{
    public function getQuestionType(Request $request, $id)
    {
        $validator = Validator::make(
            ['id' => $id],
            ['id' => 'required|string|min:36|exists:question_type,id']
        );

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $questionTypeModel = QuestionType::find($id);

        if ($questionTypeModel === null) {
            return response()->json([
                'msg' => 'URL resource not found'
            ], Response::HTTP_OK);
        }

        return response()->json([
            'questionType' => $questionTypeModel
        ], Response::HTTP_OK);
    }

    public function getAllQuestionTypes(Request $request)
    {
        $questionTypeModel = QuestionType::get();

        return response()->json(
            ['questionTypes' => $questionTypeModel],
            Response::HTTP_OK
        );
    }

    public function updateQuestionTypes(Request $request, $questionTypeId)
    {
        $validator = Validator::make(array_merge($request->all(), [
            'id' => $questionTypeId
        ]), [
            'id' => 'required|string|min:36|exists:question_type,id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $questionTypeModel = Question::find($questionTypeId);

        if ($questionTypeModel === null) {
            return response()->json([
                'msg' => 'URL resource not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $questionTypeModel->fill->input();
        $questionTypeModel->save();

        return response()->json([
            'msg' => Response::$statusTexts[Response::HTTP_OK]
        ], Response::HTTP_OK);
    }

    public function removeQuestion(Request $request, $questionTypeId)
    {
        $validator = Validator::make(
            ['id' => $questionTypeId],
            ['id' => 'required|string|min:36|exists:question_type,id']
        );

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $questionTypeModel = Form::find($questionTypeId);

        if ($questionTypeModel === null) {
            return response()->json([
                'msg' => 'URL resource was not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $questionTypeModel->delete();

        return response()->json([

        ]);
    }

    public function createQuestionType(Request $request)
    {
    }
}
