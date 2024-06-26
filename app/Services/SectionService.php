<?php

namespace App\Services;

use App\Models\Form;
use App\Models\FormSection;
use App\Models\QuestionGroupSkip;
use App\Models\Section;
use App\Models\SectionQuestionGroup;
use App\Models\Study;
use App\Models\Translation;
use App\Models\TranslationText;
use App\Services\QuestionGroupService;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\DB;

class SectionService
{
    public static function createTranslatedSection($formId, $nameTranslationId, $sortOrder, $followUpQuestionId = null, $isRepeatable = false, $maxRepetitions = 0)
    {
        $newSectionModel = new Section;

        $sectionId = Uuid::uuid4();
        $formSectionId = Uuid::uuid4();

        DB::transaction(function () use ($formId, $nameTranslationId, $sortOrder, $newSectionModel, $sectionId, $formSectionId, $isRepeatable, $followUpQuestionId, $maxRepetitions) {
            $newSectionModel->id = $sectionId;
            $newSectionModel->name_translation_id = $nameTranslationId;
            $newSectionModel->save();

            $newFormSectionModel = new FormSection;
            $newFormSectionModel->id = $formSectionId;
            $newFormSectionModel->form_id = $formId;
            $newFormSectionModel->section_id = $sectionId;
            $newFormSectionModel->sort_order = $sortOrder;
            $newFormSectionModel->is_repeatable = $isRepeatable;
            $newFormSectionModel->follow_up_question_id = $followUpQuestionId;
            $newFormSectionModel->max_repetitions = $maxRepetitions;
            $newFormSectionModel->save();
        });

        $returnSection = Form::find($formId)
            ->sections()
            ->find($sectionId);

        return $returnSection;
    }

    public function createSection($formId, $sectionName, $sortOrder)
    {
        // TODO: createSection should create a name translation and then call createTranslatedSection
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
        //$repeatPromptTranslationId = ($repeatPrompt == null) ? null : Uuid::uuid4();
        //$repeatPromptTranslationTextId = ($repeatPrompt == null) ? null : Uuid::uuid4();

        DB::transaction(function () use ($formId, $sectionName, $sortOrder, $studyLocaleId, $newSectionModel, $translationId, $translationTextId, $sectionId, $formSectionId) {
            $newTranslationModel = new Translation;
            $newTranslationModel->id = $translationId;
            $newTranslationModel->save();

            $newTranslationTextModel = new TranslationText;
            $newTranslationTextModel->id = $translationTextId;
            $newTranslationTextModel->translation_id = $translationId;
            $newTranslationTextModel->locale_id = $studyLocaleId;
            $newTranslationTextModel->translated_text = $sectionName;
            $newTranslationTextModel->save();

            /*
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
            */

            $newSectionModel->id = $sectionId;
            $newSectionModel->name_translation_id = $translationId;
            $newSectionModel->save();

            $newFormSectionModel = new FormSection;
            $newFormSectionModel->id = $formSectionId;
            $newFormSectionModel->form_id = $formId;
            $newFormSectionModel->section_id = $sectionId;
            $newFormSectionModel->sort_order = $sortOrder;
            //$newFormSectionModel->max_repetitions = 0;
            //$newFormSectionModel->repeat_prompt_translation_id = $repeatPromptTranslationId;
            $newFormSectionModel->save();
        });

        $returnSection = Form::find($formId)
            ->sections()
            ->with('nameTranslation', 'formSections', 'questionGroups')
            ->find($sectionId);

        return $returnSection;
    }

    public static function copySection (Section $section, Array &$questionMap): Section {
      $newSection = $section->replicate(['id', 'nameTranslation'])->fill([
        'id' => Uuid::uuid4(),
        'name_translation_id' => TranslationService::copyTranslation($section->nameTranslation)->id,
      ]);
      $newSection->save();
      foreach ($section->questionGroups as $group) {
        $g = QuestionGroupService::copyQuestionGroup($group, $questionMap);
        $sqg = $group->pivot->replicate(['id', 'section_id', 'question_group_id'])->fill([
          'id' => Uuid::uuid4(),
          'section_id' => $newSection->id,
          'question_group_id' => $g->id,
        ]);
        // $g->save();
        $sqg->save();
      }
      return $newSection;
    }
}
