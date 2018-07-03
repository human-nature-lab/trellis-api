<?php

namespace App\Services;

use App\Models\Form;
use App\Models\StudyForm;
use App\Models\Study;
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

    /*
    public function createCensusForm($formName, $studyId, $formMasterId) {

        $studyModel = Study::find($studyId);

        $newFormModel = new Form;

        DB::transaction(function() use ($formName, $formMasterId, $newFormModel, $studyModel, $studyId) {

            $translationId = Uuid::uuid4();
            $translationTextId = Uuid::uuid4();
            //$formId = Uuid::uuid4();
            $studyFormId = Uuid::uuid4();

            // Create new Translation.
            $newTranslationModel = new Translation;
            $newTranslationModel->id = $translationId;
            $newTranslationModel->save();

            // Create new TranslationText.
            $newTranslationTextModel = new TranslationText;
            $newTranslationTextModel->id = $translationTextId;
            $newTranslationTextModel->translation_id = $translationId;
            $newTranslationTextModel->locale_id = $studyModel->default_locale_id;
            $newTranslationTextModel->translated_text = $formName;
            $newTranslationTextModel->save();

            // Set FormMasterId.
            //if (empty($formMasterId)) {
            //    $formMasterId = $formId;
            //} else {
            //    $formMasterId = $formMasterId;
            //}

            // Set Version.
            $version = Form::where('form_master_id', '=', $formMasterId)
                ->max('version');

            if ($version !== null) {
                $version++;
                $formVersion = $version;
            } else {
                $formVersion = 1;
            }

            // Create new Form.
            $newFormModel->id = $formMasterId;
            $newFormModel->form_master_id = $formMasterId;
            $newFormModel->name_translation_id = $translationId;
            $newFormModel->version = $formVersion;
            $newFormModel->save();

            $newFormModel->translated_text = $formName;

            // Assign census form to study
            $studyModel->census_form_master_id = $formMasterId;
            $studyModel->save();
        });

        return $newFormModel;
    }
    */

    public function createForm($formName, $studyId, $formType)
    {
        $studyModel = Study::find($studyId);

        $newFormModel = new Form;

        DB::transaction(function () use ($formName, $newFormModel, $studyModel, $studyId, $formType) {
            $translationId = Uuid::uuid4();
            $translationTextId = Uuid::uuid4();
            $formId = Uuid::uuid4();
            $studyFormId = Uuid::uuid4();

            // Create new Translation.
            $newTranslationModel = new Translation;
            $newTranslationModel->id = $translationId;
            $newTranslationModel->save();

            // Create new TranslationText.
            $newTranslationTextModel = new TranslationText;
            $newTranslationTextModel->id = $translationTextId;
            $newTranslationTextModel->translation_id = $translationId;
            $newTranslationTextModel->locale_id = $studyModel->default_locale_id;
            $newTranslationTextModel->translated_text = $formName;
            $newTranslationTextModel->save();

            // TODO: Form versioning, set FormMasterId.
            /*
            if (empty($formMasterId)) {
                $formMasterId = $formId;
            } else {
                $formMasterId = $formMasterId;
            }
            */

            $formMasterId = $formId;

            // Set Version.
            $version = Form::where('form_master_id', '=', $formMasterId)
                ->max('version');

            if ($version !== null) {
                $version++;
                $formVersion = $version;
            } else {
                $formVersion = 1;
            }

            // Create new Form.
            $newFormModel->id = $formId;
            $newFormModel->form_master_id = $formMasterId;
            $newFormModel->name_translation_id = $translationId;
            $newFormModel->version = $formVersion;
            $newFormModel->save();

            $newFormModel->translated_text = $formName;

            // Create new StudyForm.
            $newStudyFormModel = new StudyForm;
            $newStudyFormModel->id = $studyFormId;
            $newStudyFormModel->study_id = $studyId;
            $newStudyFormModel->form_master_id = $formMasterId;

            $maxSortOrder = DB::table('study_form')
                ->where('study_id', '=', $studyId)
                ->whereNull('deleted_at')
                ->max('sort_order');

            if ($maxSortOrder == null) {
                $maxSortOrder = 0;
            }

            $newStudyFormModel->sort_order = $maxSortOrder + 1;

            $newStudyFormModel->form_type_id = 1*$formType;
            $newStudyFormModel->save();
        });

        return $newFormModel;
    }

    /*
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
    */
}
