<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Validator;
use App\Models\QuestionGroup;
use App\Models\SectionQuestionGroup;
use DB;
class QuestionGroupController extends Controller
{

	public function getQuestionGroup(Request $request, $id) {

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

	public function getAllQuestionGroups(Request $request, $formId, $localeId) {

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

		$questionGroupModel = QuestionGroup::select('question_group.id', 'sqg.question_group_order AS sort_order', 'fs.section_id')
				->join('section_question_group AS sqg', 'question_group_id', '=', 'question_group.id')
				->join('form_section AS fs', 'fs.section_id', '=', 'sqg.section_id')
				->where('fs.form_id', $formId)
				->orderBy('sqg.question_group_order', 'asc')
				->get();

		return response()->json(
			['questionGroups' => $questionGroupModel],
			Response::HTTP_OK
		);
	}

	public function removeQuestionGroup(Request $request, $group_id) {

		$validator = Validator::make(
			['id' => $group_id],
			['id' => 'required|string|min:36']
		);

		if ($validator->fails() === true) {
			return response()->json([
				'msg' => 'Validation failed',
				'err' => $validator->errors()
			], $validator->statusCode());
		}

		$questionGroupModel = QuestionGroup::find($group_id);

		if ($questionGroupModel === null) {
			return response()->json([
				'msg' => 'URL resource was not found'
			], Response::HTTP_NOT_FOUND);
		}

		$questionGroupModel->delete();

		return response()->json([

		]);
	}

	public function createQuestionGroup(Request $request, $sectionId) {

		$validator = Validator::make(array_merge($request->all(), [
				'section_id' => $sectionId]), [
				'section_id' => 'required|string|min:36|exists:section,id'
		]);

		if ($validator->fails() === true) {
			return response()->json([
					'msg' => 'Validation failed',
					'err' => $validator->errors()
			], $validator->statusCode());
		}

		$questionGroupId = Uuid::uuid4();
		$sectionQuestionGroupId = Uuid::uuid4();

		$questionGroupModel = new QuestionGroup;

		DB::transaction(function() use($request, $questionGroupId, $sectionQuestionGroupId, $questionGroupModel, $sectionId) {

			$questionGroupModel->id = $questionGroupId;
			$questionGroupModel->save();
			$questionGroupModel->section_id = $sectionId;

			$sectionQuestionGroupModel = new SectionQuestionGroup;
			$sectionQuestionGroupModel->id = $sectionQuestionGroupId;
			$sectionQuestionGroupModel->section_id = $sectionId;
			$sectionQuestionGroupModel->question_group_id = $questionGroupId;
            $maxQuestionGroupOrder = DB::table('section_question_group')
                ->where('section_id', '=', $sectionId)
                ->whereNull('deleted_at')
                ->max('question_group_order');

			//$sectionQuestionGroupModel->question_group_order = 1;
            $sectionQuestionGroupModel->question_group_order = $maxQuestionGroupOrder + 1;
			$sectionQuestionGroupModel->save();
		});

		return response()->json([
			'questionGroup' => $questionGroupModel
		], Response::HTTP_OK);
	}

	public function updateQuestionGroup(Request $request, $id) {

		$validator = Validator::make(array_merge($request->all(),[
				'id' => $id
		]), [
				'id' => 'required|string|min:36'
		]);

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
			], Response::HTTP_NOT_FOUND);
		}

		$questionGroupModel->fill($request->input());
		$questionGroupModel->save();

		return response()->json([
				'msg' => Response::$statusTexts[Response::HTTP_OK]
		], Response::HTTP_OK);
	}


    public function updateSectionQuestionGroups(Request $request) {
        // PATCH method for updating multiple section_question_group rows at once
        // Should be provided an array of section question group objects with UID and one or more fields to be updated
        // TODO: Validate that each question provided has a valid ID, easier when upgrading to laravel 5.3+
        $validator = Validator::make($request->all(), [
            'sectionQuestionGroups' => 'required|array'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $sectionQuestionGroups = $request->input('sectionQuestionGroups');

        DB::transaction(function () use ($sectionQuestionGroups) {
            foreach($sectionQuestionGroups as $sectionQuestionGroup) {
                DB::table('section_question_group')
                    ->where('section_id', $sectionQuestionGroup['section_id'])
                    ->where('question_group_id', $sectionQuestionGroup['question_group_id'])
                    ->whereNull('deleted_at')
                    ->update($sectionQuestionGroup);
            }
        });

        return response()->json([
            'msg' => Response::$statusTexts[Response::HTTP_OK]
        ], Response::HTTP_OK);
    }
}
