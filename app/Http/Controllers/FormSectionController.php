<?php

namespace App\Http\Controllers;

use App\Services\TranslationService;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;
use App\Models\FormSection;
use App\Models\Section;


class FormSectionController extends Controller
{
	public function updateFormSection(Request $request, TranslationService $translationService, $formSectionId) {

		$validator = Validator::make(array_merge($request->all(),[
			'id' => $formSectionId,
		]), [
			'id' => 'required|string|min:36|exists:form_section,id'
		]);

		if ($validator->fails() === true) {
			return response()->json([
				'msg' => 'Validation failed',
				'err' => $validator->errors()
			], $validator->statusCode());
		}

		$formSectionModel = FormSection::find($formSectionId);

		if ($formSectionModel === null) {
			return response()->json([
				'msg' => 'URL resource not found'
			], Response::HTTP_NOT_FOUND);
		}

		$formSectionModel->fill($request->input());
        // If repeat_prompt_translation is null, create a translation element
        if ($formSectionModel->repeat_prompt_translation_id == null) {
            $translationId = $translationService->createNewTranslation();
            $formSectionModel->repeat_prompt_translation_id = $translationId;
        }
		$formSectionModel->save();

		// Return the section
		$returnSection = Section::with('questionGroups', 'nameTranslation', 'formSections.repeatPromptTranslation')
            ->find($formSectionModel->section_id);

		return response()->json([
			'msg' => Response::$statusTexts[Response::HTTP_OK],
            'section' => $returnSection
		], Response::HTTP_OK);
	}
}
