<?php

namespace App\Services;

use App\Models\Form;
use App\Models\Section;
use App\Models\StudyForm;
use App\Models\Study;
use App\Models\TranslationText;
use App\Models\Translation;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use DB;
use Ramsey\Uuid\Uuid;
use Throwable;

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

    public static function importFormAndAddToStudy ($filePath, $formName, $studyId, $formType) {
        try {
            DB::beginTransaction();
            $study = Study::find($studyId);
            $importedForm = self::importFormFromPath($filePath, $formName, $study->default_locale_id);
            self::createStudyForm($importedForm->form_master_id, $studyId, $formType);
            DB::commit();
        } catch (Throwable $e) {
            Log::error($e);
            DB::rollBack();
            throw $e;
        }
        return $importedForm;
    }

    public static function createStudyForm ($formId, $studyId, $formType) {
        $studyForm = new StudyForm;
        $studyForm->id = Uuid::uuid4();
        $studyForm->study_id = $studyId;
        $studyForm->form_master_id = $formId;

        $maxSortOrder = DB::table('study_form')
            ->where('study_id', '=', $studyId)
            ->whereNull('deleted_at')
            ->max('sort_order');

        if ($maxSortOrder == null) {
            $maxSortOrder = 0;
        }

        $studyForm->sort_order = $maxSortOrder + 1;

        $studyForm->form_type_id = 1*$formType;

        $studyForm->save();
        return $studyForm;
    }

    public static function importFormFromPath ($filePath, $formName, $localeId) {
        $oldQuestionIdToNewQuestionIdMap = []; // Used for follow up questions
        try {
            DB::beginTransaction();

            // Create the form
            $importedForm = self::createForm($formName, $localeId);
            $importedFormId = $importedForm->form_master_id;
            $formJsonString = file_get_contents($filePath);
            $jsonObject = json_decode($formJsonString, true);
            $formObject = $jsonObject["form"];

            // It is essential to insert in the same order that the form is conducted in so that follow up questions exist
            // before their follow up sections
            uasort($formObject["sections"], function ($a, $b) {
                return $a['form_sections'][0]["sort_order"] - $b['form_sections'][0]["sort_order"];
            });

            foreach ($formObject["sections"] as $sectionObject) {
                $sectionSortOrder = $sectionObject["form_sections"][0]["sort_order"];
                $maxRepetitions = $sectionObject['form_sections'][0]['max_repetitions'];
                $isRepeatable = $sectionObject['form_sections'][0]['is_repeatable'];

                // This will fail if the sections with follow up questions aren't inserted after the referenced questions
                $newFollowUpQuestionId = $sectionObject['form_sections'][0]['follow_up_question_id'] ? $oldQuestionIdToNewQuestionIdMap[$sectionObject['form_sections'][0]['follow_up_question_id']] : null;

                $sectionNameTranslationId = TranslationService::importTranslation($sectionObject["name_translation"]);
                $importedSection = SectionService::createTranslatedSection($importedFormId, $sectionNameTranslationId, $sectionSortOrder, $newFollowUpQuestionId, $isRepeatable, $maxRepetitions);

                foreach ($sectionObject["question_groups"] as $questionGroupObject) {
                    $questionGroupSortOrder = $questionGroupObject["pivot"]["question_group_order"];
                    $importedQuestionGroup = QuestionGroupService::createQuestionGroup($importedSection["id"], $questionGroupSortOrder);

                    foreach ($questionGroupObject["skips"] as $skipObject) {
                        $importedSkip = SkipService::createSkip($importedQuestionGroup["id"], $skipObject["show_hide"], $skipObject["any_all"], $skipObject["precedence"]);

                        foreach ($skipObject["conditions"] as $skipConditionTagObject) {
                            SkipService::createSkipConditionTag($importedSkip["id"], $skipConditionTagObject["condition_tag_name"]);
                        }
                    }

                    foreach ($questionGroupObject["questions"] as $questionObject) {
                        $questionTranslationId = TranslationService::importTranslation($questionObject["question_translation"]);
                        $importedQuestion = QuestionService::createTranslatedQuestion($importedQuestionGroup["id"], $questionTranslationId, $questionObject["var_name"], $questionObject["question_type"]["id"], $questionObject["sort_order"]);
                        $oldQuestionIdToNewQuestionIdMap[$questionObject['id']] = $importedQuestion['id'];
                        foreach ($questionObject["question_parameters"] as $questionParameterObject) {
                            QuestionParameterService::createQuestionParameter($importedQuestion["id"], $questionParameterObject["parameter_id"], $questionParameterObject["val"]);
                        }

                        foreach ($questionObject["assign_condition_tags"] as $assignConditionTagObject) {
                            $importedConditionTag = ConditionTagService::createConditionTag($assignConditionTagObject['condition']['name']);
                            AssignConditionTagService::createAssignConditionTag($importedQuestion["id"], $importedConditionTag["id"], $assignConditionTagObject["logic"], $assignConditionTagObject["scope"]);
                        }

                        foreach ($questionObject["choices"] as $choiceObject) {
                            $choiceTranslationId = TranslationService::importTranslation($choiceObject['choice_translation']);
                            QuestionChoiceService::createTranslatedQuestionChoice($importedQuestion["id"], $choiceTranslationId, $choiceObject["val"], $choiceObject["pivot"]["sort_order"]);
                        }
                    }
                }
            }

            DB::commit();
        } catch (QueryException $e) {
            Log::error($e);
            DB::rollBack();
            throw $e;
        } catch (Throwable $e) {
            Log::error($e);
            DB::rollBack();
            throw $e;
        }

        return $importedForm;
    }

    public static function createForm($formName, $localeId) {
        $newFormModel = null;

        try {
            DB::beginTransaction();
            $translationId = Uuid::uuid4();
            $translationTextId = Uuid::uuid4();
            $formId = Uuid::uuid4()->toString();

            // Create new Translation.
            $newTranslationModel = new Translation;
            $newTranslationModel->id = $translationId;
            $newTranslationModel->save();

            // Create new TranslationText.
            $newTranslationTextModel = new TranslationText;
            $newTranslationTextModel->id = $translationTextId;
            $newTranslationTextModel->translation_id = $translationId;
            $newTranslationTextModel->locale_id = $localeId;
            $newTranslationTextModel->translated_text = $formName;
            $newTranslationTextModel->save();

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
            $newFormModel = Form::create([
                'id' => $formId,
                'form_master_id' => $formMasterId,
                'name_translation_id' => $translationId,
                'version' => $formVersion
            ]);

            DB::commit();

        } catch (Throwable $e) {
            Log::error($e);
            DB::rollBack();
        }

        return $newFormModel;
    }

    /**
     * Creates both a form entry and a study_form entry within a transaction
     * @param $formName
     * @param $studyId
     * @param $formType
     * @return null
     */
    public static function createFormWithStudyForm ($formName, $studyId, $formType) {
        $form = null;

        DB::transaction(function () use ($formName, $studyId, $formType, &$form) {
            $study = Study::find($studyId);
            $form = self::createForm($formName, $study->default_locale_id);
            $studyForm = self::createStudyForm($form->id, $studyId, $formType);
        });

        return $form;
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
