<?php

namespace App\Services;

use App\Models\Form;
use App\Models\StudyForm;
use App\Models\Study;
use App\Models\TranslationText;
use App\Models\Translation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Throwable;

class FormService {

  public static function getAllStudyForms(String $studyId) {
    $forms = Form::where('study.id', $studyId)
      ->leftJoin('study_form', 'study_form.id', '=', 'form.id')
      ->join('study', 'study.id', '=', 'study_form.study_id')
      ->get();

    return $forms;
  }

  public static function getAllStudyFormsPaginated($perPage, String $studyId) {
    $forms = Form::select('tt.translated_text AS name', 'f.version', 'f.updated_at', 'f.id')
      ->from('translation_text AS tt')
      ->join('form AS f', 'tt.translation_id', '=', 'f.name_translation_id')
      ->join('study_form AS sf', 'sf.form_master_id', '=', 'f.form_master_id')
      ->where('sf.study_id', $studyId)
      ->orderBy('f.created_at')
      ->paginate($perPage);

    return $forms;
  }

  public static function importFormAndAddToStudy(String $filePath, String $formName, String $studyId, String $formType) {
    $study = Study::find($studyId);
    $importedForm = self::importFormFromPath($filePath, $formName, $study->default_locale_id);
    self::createStudyForm($importedForm->form_master_id, $studyId, $formType);
    return $importedForm;
  }

  public static function createStudyForm(String $formId, String $studyId, String $formType) {
    $studyForm = new StudyForm;
    $studyForm->id = Uuid::uuid4();
    $studyForm->study_id = $studyId;
    $studyForm->form_master_id = $formId;

    $formTypeId = 1 * $formType;
    $maxSortOrder = DB::table('study_form')
      ->where('study_id', '=', $studyId)
      ->where('form_type_id', '=', $formTypeId)
      ->whereNull('deleted_at')
      ->max('sort_order');

    if ($maxSortOrder == null) {
      $maxSortOrder = 0;
    }

    $studyForm->sort_order = $maxSortOrder + 1;

    $studyForm->form_type_id = $formTypeId;

    $studyForm->save();
    return $studyForm;
  }

  public static function importFormFromPath(String $filePath, String $formName, String $localeId) {
    $oldQuestionIdToNewQuestionIdMap = []; // Used for follow up questions

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

    return $importedForm;
  }

  public static function createFormWithTranslation (Translation $tr, String $formMasterId, int $version): Form {
    
  }

  public static function createForm (String $formName, String $localeId): Form {
    $newFormModel = null;

    try {
      DB::beginTransaction();
      $formId = Uuid::uuid4();
      $translationId = Uuid::uuid4();
      $translationTextId = Uuid::uuid4();
  
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

      $newFormModel =  Form::create([
        'id' => $formId,
        'form_master_id' => $formMasterId,
        'name_translation_id' => $translationId,
        'version' => 1
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
   */
  public static function createFormWithStudyForm(String $formName, String $studyId, String $formType) {
    $form = null;

    DB::transaction(function () use ($formName, $studyId, $formType, &$form) {
      $study = Study::find($studyId);
      $form = self::createForm($formName, $study->default_locale_id);
      $studyForm = self::createStudyForm($form->id, $studyId, $formType);
    });

    return $form;
  }

  /**
   * Copy a form in the database
   */
  public static function copyForm (Form $form, int $version): Form {
    $formId = Uuid::uuid4();
    $form = Form::create([
      'id' => $formId,
      'form_master_id' => $form->form_master_id,
      'name_translation_id' => $form->name_translation_id,
      'published' => true,
      'version' => $version,
    ]);

    $sections = [];
    foreach ($form->sections as $section) {
      $sections[] = SectionService::copySection($section);
    }

    $form->sections()->saveMany($sections);

    return $form;
  }
}
