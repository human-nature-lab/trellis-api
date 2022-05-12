<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Section;
use App\Models\StudyForm;
use App\Models\FormSection;
use App\Services\SectionService;

class SectionController extends Controller
{
    public function getSection(Request $request, $id)
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

    public function getStudyFormSections($studyId)
    {
        $validator = Validator::make( [
          'studyId' => $studyId,
        ], [
          'studyId' => 'required|string|min:36|exists:study,id',
        ]);

        if ($validator->fails() === true) {
          return response()->json([
            'msg' => 'Validation failed',
            'err' => $validator->errors(),
          ], $validator->statusCode());
        }

        $forms = StudyForm::with('form.nameTranslation', 'form.sections.nameTranslation')->where('study_id', $studyId)->get();


        return response()->json([
          'study_forms' => $forms,
        ], Response::HTTP_OK);
    }

    public function updateSection(Request $request, $sectionId)
    {
        $validator = Validator::make(array_merge($request->all(), [
            'id' => $sectionId,
        ]), [
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

        $sectionModel->fill($request->input());
        $sectionModel->save();

        return response()->json([
            'msg' => Response::$statusTexts[Response::HTTP_OK]
        ], Response::HTTP_OK);
    }

    public function updateSections(Request $request)
    {
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
            foreach ($sections as $section) {
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

    public function removeSection($formSectionId)
    {
        $validator = Validator::make(
            ['id' => $formSectionId],
            ['id' => 'required|string|min:36|exists:form_section,id']
        );

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        };

        $sectionModel = FormSection::find($formSectionId);

        if ($sectionModel === null) {
            return response()->json([
                'msg' => 'URL resource was not found'
            ], Response::HTTP_NOT_FOUND);
        };

        $sectionModel->delete();

        return response()->json([

        ]);
    }


    public function createSection(Request $request, SectionService $sectionService, $formId)
    {
        $validator = Validator::make(array_merge($request->all(), [
            'formId' => $formId]), [
            'formId' => 'required|string|min:36|exists:form,id',
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $returnSection = $sectionService->createSection(
            $formId,
            '',
            $request->input('sort_order')
        );

        return response()->json([
            'section' => $returnSection
        ], Response::HTTP_OK);
    }
}
