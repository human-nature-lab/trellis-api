<?php

namespace App\Services;

use App\Models\Form;
use App\Models\StudyForm;
use App\Models\TranslationText;
use App\Models\Translation;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use DB;


class FormService
{

    public static function getAllForms()
    {

    }

    public static function getAllStudyForms($studyId)
    {
        $forms = Form::where('study.id', $studyId)
            ->leftJoin('study_form', 'study_form.id', '=', 'form.id')
            ->join('study', 'study.id', '=', 'study_form.study_id')
            ->get();

        return $forms;
    }

    public static function getAllStudyFormsPaginated($perPage, $studyId)
    {
        $forms = Form::select('tt.translated_text AS name', 'f.version', 'f.updated_at', 'f.id')
            ->from('translation_text AS tt')
            ->join('form AS f', 'tt.translation_id', '=', 'f.name_translation_id')
            ->join('study_form AS sf', 'sf.form_master_id', '=', 'f.form_master_id')
            ->where('sf.study_id', $studyId)
            ->orderBy('f.created_at')
            ->paginate($perPage);

        return $forms;
    }

    public static function createNewForm($request, $studyId, $translationId = null)
    {
        // Check for existing translation record.
        if ($translationId == null) {
            // Insert Translation record.
            $translation = new Translation();

            $translationId = Uuid::uuid4();
            $translation->id = $translationId;

            $translation->save();
        }

        // Insert Translation_Text record.
        $translationText = new TranslationText();

        $translationText->id = Uuid::uuid4();
        $translationText->translation_id = $translationId;
        $translationText->locale_id = $request->input('locale_id');
        $translationText->translated_text = $request->input('name');

        $translationText->save();

        // Insert Form record.
        $form = new Form();

        $formId = Uuid::uuid4();
        $form->id = $formId;
        $form->form_master_id = $form->id;
        $form->name_translation_id = $translationId;
        $form->version = '1';

        $form->save();

        // Insert Study-Form record.
        $studyForm = new StudyForm();

        $studyForm->id = Uuid::uuid4();
        $studyForm->study_id = $studyId;
        $studyForm->form_master_id = $formId;
        $studyForm->sort_order = 0;

        $studyForm->save();

        return $form;
    }
}