<?php

namespace App\Http\Controllers;

use App\Models\SectionQuestionGroup;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Validator;
use DB;
use App\Models\Section;
use App\Models\FormSection;
use App\Models\Study;
use App\Models\Translation;
use App\Models\TranslationText;

class SectionController extends Controller
{

	public function getSection(Request $request, $id) {

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

		$sectionModel = Section::find($id);

		if ($sectionModel === null) {
			return response()->json([
					'msg' => 'URL resource not found'
			], Response::HTTP_OK);
		}

		return response()->json([
				'section' => $sectionModel
		], Response::HTTP_OK
		);
	}

	public function getAllSections(Request $request, $formId, $localeId) {

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

			$sectionModel = Section::select('section.id', 'tt.translated_text AS text', 'fs.sort_order AS sort_order')
					->join('form_section AS fs', 'fs.section_id', '=', 'section.id')
					->join('translation_text AS tt', 'tt.translation_id', '=', 'section.name_translation_id')
					->where('fs.form_id', $formId)
					->where('tt.locale_id', $localeId)
					->orderBy('fs.sort_order', 'asc')
					->get();

		return response()->json(
			['sections' => $sectionModel
			], Response::HTTP_OK
		);
	}

	public function updateSection(Request $request, $formId, $sectionId) {

		$validator = Validator::make(array_merge($request->all(),[
			'form_id' => $formId,
			'section_id' => $sectionId,
		]), [
			'form_id' => 'required|string|min:36|exists:form,id',
			'id' => 'required|string|min:36|exists:section,id'
		]);

		if ($validator->fails() === true) {
			return response()->json([
				'msg' => 'Validation failed',
				'err' => $validator->errors()
			], $validator->statusCode());
		}

		$sectionModel = Section::find($id);

		if ($sectionModel === null) {
			return response()->json([
				'msg' => 'URL resource not found'
			], Response::HTTP_NOT_FOUND);
		}

		DB::transaction(function() use($request, $sectionModel) {
			if (!empty($request->input('translated_text')))
				$translationText = TranslationText::where('id', $sectionModel->name_translation_id)
						->update(['translated_text' => $request->input('translated_text')]);



		});


		$sectionModel->fill($request->input());
		$sectionModel->save();

		return response()->json([
			'msg' => Response::$statusTexts[Response::HTTP_OK]
		], Response::HTTP_OK);
	}

	public function removeSection(Request $request, $id) {

		$validator = Validator::make(
			['id' => $id],
			['id' => 'required|string|min:36|exists:section,id']
		);

		if ($validator->fails() === true) {
			return response()->json([
				'msg' => 'Validation failed',
				'err' => $validator->errors()
			], $validator->statusCode());
		};

		$sectionQuestionGroupModel = SectionQuestionGroup::where('section_id', $id)
				->first();

		if ($sectionQuestionGroupModel !== null) {
			return response()->json([
				'msg' => 'Unable to delete Section. Please delete all child Question Groups before proceeding.'
			], Response::HTTP_CONFLICT);
		}

		$sectionModel = Section::find($id);

		if ($sectionModel === null) {
			return response()->json([
				'msg' => 'URL resource was not found'
			], Response::HTTP_NOT_FOUND);
		};

		$sectionModel->delete();

		return response()->json([

		]);
	}

	public function createSection(Request $request, $formId) {

		$validator = Validator::make(array_merge($request->all(), [
			'form_id' => $formId]), [
			'form_id' => 'required|string|min:36',
			'translated_text' => 'required|string|min:1',
			'max_repetitions' => 'integer|min:0'
		]);

		if ($validator->fails() === true) {
			return response()->json([
				'msg' => 'Validation failed',
				'err' => $validator->errors()
			], $validator->statusCode());
		}

		$studyModel = Study::select('study.*')
					->join('study_form AS sf', 'sf.study_id', '=', 'study.id')
					->join('form AS f', 'f.id', '=', 'sf.form_master_id')
					->where('f.id', $formId)
					->first();

		$studyLocaleId = $studyModel->default_locale_id;

		$newSectionModel = new Section;

		$translationId = Uuid::uuid4();
		$translationTextId = Uuid::uuid4();
		$sectionId = Uuid::uuid4();
		$formSectionId = Uuid::uuid4();
		$repeatPromptSet = $request->input('repeat_prompt_translation_text');
		$repeatPromptTranslationId = $repeatPromptSet = null ? null : Uuid::uuid4();
		$repeatPromptTranslationTextId = $repeatPromptSet = null ? null : Uuid::uuid4();

		DB::transaction(function() use($request, $studyLocaleId, $newSectionModel, $translationId, $translationTextId, $repeatPromptTranslationTextId, $repeatPromptTranslationId, $sectionId, $formSectionId, $formId) {

			$newTranslationModel = new Translation;
			$newTranslationModel->id = $translationId;
			$newTranslationModel->save();

			$newTranslationTextModel = new TranslationText;
			$newTranslationTextModel->id = $translationTextId;
			$newTranslationTextModel->translation_id = $translationId;
			$newTranslationTextModel->locale_id = $studyLocaleId;
			$newTranslationTextModel->translated_text = $request->input('translated_text');
			$newTranslationTextModel->save();

			if ($request->input('repeat_prompt_translation_text') != null) {
				$newRepeatPromptTranslationModel = new Translation;
				$newRepeatPromptTranslationModel->id = $repeatPromptTranslationId;
				$newRepeatPromptTranslationModel->save();

				$newRepeatPromptTranslationTextModel = new TranslationText;
				$newRepeatPromptTranslationTextModel->id = $repeatPromptTranslationTextId;
				$newRepeatPromptTranslationTextModel->translation_id = $repeatPromptTranslationId;
				$newRepeatPromptTranslationTextModel->locale_id = $studyLocaleId;
				$newRepeatPromptTranslationTextModel->translated_text = $request->input('repeat_prompt_translation_text');
				$newRepeatPromptTranslationTextModel->save();
			}

			$newSectionModel->id = $sectionId;
			$newSectionModel->name_translation_id = $translationId;
			$newSectionModel->save();

			$newFormSectionModel = new FormSection;
			$newFormSectionModel->id = $formSectionId;
			$newFormSectionModel->form_id = $formId;
			$newFormSectionModel->section_id = $sectionId;
			$newFormSectionModel->sort_order = $request->input('sort_order');
			$newFormSectionModel->max_repetitions = $request->input('max_repetitions');
			$newFormSectionModel->repeat_prompt_translation_id = $request->input('repeat_prompt_');
			$newFormSectionModel->save();

			$newSectionModel->translated_text = $request->input('translated_text');
			$newSectionModel->sort_order = $request->input('sort_order');
		});

		return response()->json([
			'section' => $newSectionModel
		], Response::HTTP_OK);
	}
}
