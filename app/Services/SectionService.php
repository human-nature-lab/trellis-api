<?php

namespace App\Services;

use App\Models\Form;
use App\Models\FormSection;
use App\Models\Section;
use App\Models\Study;
use App\Models\Translation;
use App\Models\TranslationText;
use Ramsey\Uuid\Uuid;
use DB;


class SectionService
{
    public function createSection($formId, $sectionName, $maxRepetitions, $repeatPrompt, $sortOrder) {

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
        $repeatPromptTranslationId = ($repeatPrompt == null) ? null : Uuid::uuid4();
        $repeatPromptTranslationTextId = ($repeatPrompt == null) ? null : Uuid::uuid4();

        DB::transaction(function() use($formId, $sectionName, $maxRepetitions, $repeatPrompt, $sortOrder, $studyLocaleId, $newSectionModel, $translationId, $translationTextId, $repeatPromptTranslationTextId, $repeatPromptTranslationId, $sectionId, $formSectionId) {

            $newTranslationModel = new Translation;
            $newTranslationModel->id = $translationId;
            $newTranslationModel->save();

            $newTranslationTextModel = new TranslationText;
            $newTranslationTextModel->id = $translationTextId;
            $newTranslationTextModel->translation_id = $translationId;
            $newTranslationTextModel->locale_id = $studyLocaleId;
            $newTranslationTextModel->translated_text = $sectionName;
            $newTranslationTextModel->save();

            if ($repeatPrompt != null) {
                $newRepeatPromptTranslationModel = new Translation;
                $newRepeatPromptTranslationModel->id = $repeatPromptTranslationId;
                $newRepeatPromptTranslationModel->save();

                $newRepeatPromptTranslationTextModel = new TranslationText;
                $newRepeatPromptTranslationTextModel->id = $repeatPromptTranslationTextId;
                $newRepeatPromptTranslationTextModel->translation_id = $repeatPromptTranslationId;
                $newRepeatPromptTranslationTextModel->locale_id = $studyLocaleId;
                $newRepeatPromptTranslationTextModel->translated_text = $repeatPrompt;
                $newRepeatPromptTranslationTextModel->save();
            }

            $newSectionModel->id = $sectionId;
            $newSectionModel->name_translation_id = $translationId;
            $newSectionModel->save();

            $newFormSectionModel = new FormSection;
            $newFormSectionModel->id = $formSectionId;
            $newFormSectionModel->form_id = $formId;
            $newFormSectionModel->section_id = $sectionId;
            $newFormSectionModel->sort_order = $sortOrder;
            $newFormSectionModel->max_repetitions = $maxRepetitions;
            $newFormSectionModel->repeat_prompt_translation_id = $repeatPromptTranslationId;
            $newFormSectionModel->save();
        });

        $returnSection = Form::find($formId)
            ->sections()
            ->find($sectionId);

        return $returnSection;
    }
}