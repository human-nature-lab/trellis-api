<?php

namespace App\Http\Controllers;

use App\Models\SectionQuestionGroup;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Validator;
use DB;
use App\Models\Form;
use App\Models\Section;
use App\Models\FormSection;
use App\Models\Study;
use App\Models\Translation;
use App\Models\TranslationText;
use App\Services\SectionService;


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

		$sectionModel = Section::find($sectionId);

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

    public function updateSections(Request $request) {
        // PATCH method for updating multiple form_section rows at once
        // Should be provided an array of objects with form_id, section_id, and one or more fields to be updated
        // TODO: Validate that each question provided has a valid ID, easier when upgrading to laravel 5.3+
        $validator = Validator::make($request->all(), [
            'sections' => 'required|array'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $sections = $request->input('sections');

        DB::transaction(function () use ($sections) {
            foreach($sections as $section) {
                DB::table('form_section')
                    ->where('form_id', $section['form_id'])
                    ->where('section_id', $section['section_id'])
                    ->whereNull('deleted_at')
                    ->update($section);
            }
        });

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


	public function createSection(Request $request, SectionService $sectionService, $formId) {

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

        $returnSection = $sectionService->createSection(
            $formId,
            $request->input('translated_text'),
            $request->input('max_repetitions'),
            $request->input('repeat_prompt_translation_text'),
            $request->input('sort_order')
        );

		return response()->json([
			'section' => $returnSection
		], Response::HTTP_OK);
	}
}
