<?php

namespace app\Http\Controllers;

use Ramsey\Uuid\Uuid;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;
use DB;

use App\Models\AssignConditionTag, App\Models\Choice, App\Models\ConditionTag, App\Models\Datum, App\Models\DatumPhoto,
    App\Models\DatumType, App\Models\Edge, App\Models\EdgeDatum, App\Models\Form, App\Models\FormSection, App\Models\FormSkip,
    App\Models\Geo, App\Models\GeoPhoto, App\Models\GeoType, App\Models\GroupTag, App\Models\GroupTagType,
    App\Models\Interview, App\Models\InterviewQuestion, App\Models\Locale, App\Models\Parameter, App\Models\Photo,
    App\Models\PhotoTag, App\Models\Question, App\Models\QuestionAssignConditionTag, App\Models\QuestionChoice,
    App\Models\QuestionGroup, App\Models\QuestionGroupSkip, App\Models\QuestionParameter, App\Models\QuestionType,
    App\Models\Respondent, App\Models\RespondentConditionTag, App\Models\RespondentGroupTag, App\Models\RespondentPhoto,
    App\Models\Section, App\Models\SectionQuestionGroup, App\Models\Skip, App\Models\SkipConditionTag, App\Models\Study,
    App\Models\StudyForm, App\Models\StudyLocale, App\Models\StudyRespondent, App\Models\Survey, App\Models\SurveyConditionTag,
    App\Models\Tag, App\Models\Translation, App\Models\TranslationText, App\Models\User, App\Models\UserStudy;

class SyncController extends Controller
{

    public function heartbeat()
    {

        return response()->json([], Response::HTTP_OK);

    }

    public function store(Request $request, $deviceId)
    {

        $validator = Validator::make(array_merge($request->all(), [
            'id' => $deviceId
        ]), [
            'id' => 'required|string|min:36|exists:device,device_id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

    }

    public function download(Request $request, $deviceId)
    {
        $validator = Validator::make(array_merge($request->all(), [
            'id' => $deviceId
        ]), [
            'id' => 'required|string|min:14|exists:device,device_id',
            'table' => 'required|string|min:1',
            'continuationToken' => 'string|min:1'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        };

        $response = [];

        $response["deviceId"] = $deviceId;
        $response["table"] = $request->input('table');
        $response["continuationToken"] = null;

        $tableClass = str_replace(' ', '', ucwords(str_replace('_', ' ', $request->input('table'))));
        $className = "\\App\\Models\\$tableClass";
        $classModel = $className::all();

        $response["numRows"] = $classModel->count();
        $response["totalRows"] = $classModel->count();
        $response["rows"] = $classModel;

        return response()->json($response, Response::HTTP_OK);
    }

    public function upload(Request $request, $deviceId)
    {

        $validator = Validator::make(array_merge($request->all(), [
            'id' => $deviceId
        ]), [
            'id' => 'required|string|min:14|exists:device,device_id',
            'table' => 'required|string|min:1',
            'continuationToken' => 'string|min:1'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        };

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        foreach ($request->input('rows') as $row) {
            $newClassName = "\\App\\Models\\" . str_replace(' ', '', str_replace('_', '', ucwords($request->input('table'), '_')));
            $newClassName::create($row);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        return response()->json([], Response::HTTP_OK);

    }
}