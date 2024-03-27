<?php

namespace App\Http\Controllers;

use App\Services\MarkdownService;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use App\Models\Form;
use App\Models\Locale;
use App\Models\Study;
use App\Models\StudyForm;
use App\Models\Translation;
use App\Models\TranslationText;
use App\Services\MarkdownConfig;
use App\Services\CsvService;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;

class ExportController extends Controller {

  // public function exportMarkdown(Request $request, MarkdownService $mdService) {
  //   $formIds = $request->input('ids');

  //   $mdConfig = new MarkdownConfig();
  //   $mdConfig->localeId = $request->input('localeId');

  //   // TODO
  //   // 1. Convert each form into a markdown document
  //   // 2. Zip all markdown documents into a single file
  //   // 3. Return the zip file

  //   // Create in-memory zip file
  //   $zip = new \ZipArchive();
  //   $zipFileName = 'forms.zip';
  //   $zip->open($zipFileName, \ZipArchive::CREATE);

  //   foreach ($formIds as $formId) {
  //     $form = Form::with(
  //       'sections',
  //       'nameTranslation',
  //       'sections.questionGroups',
  //       'sections.nameTranslation',
  //       'sections.formSections.repeatPromptTranslation'
  //     )->find($formId);
  //     $formName = $this->getFormFileName($form, $locale, 'md');
  //     $markdown = $mdService->formToMarkdown($form);
  //     $zip->addFromString($formName, $markdown);
  //   }

  //   $zip->close();
  //   return response()->download($zipFileName)->deleteFileAfterSend(true);
  // }
  
  public function exportFormTranslations(Request $request, String $studyId) {
    $formIds = $request->input('ids');

    $validator = Validator::make([
      'ids' => $formIds,
    ], [
      'ids' => 'required|array',
      'ids.*' => 'required|string',
    ]);
    
    if ($validator->fails()) {
      return response()->json($validator->errors(), 400);
    }

    $study = Study::find($studyId);
    $locales = $this->studyLocales($studyId);

    $mainLocale = $locales->first(function($locale) use ($study) {
      return $locale->id === $study->default_locale_id;
    });
    return new StreamedResponse(function() use ($formIds, $locales, $mainLocale) {
      $zip = new \ZipStream\ZipStream('trellis_translations.zip');
      foreach ($formIds as $formId) {
        $form = $this->getForm($formId);
        $csv = $this->formTranslationsToCsv($form, $locales);
        $zip->addFile($this->getFormFileName($form, $mainLocale, 'csv'), $csv);
      }
      $zip->finish();
    });
  }

  public function importFormTranslations (Request $request, String $studyId) {
    if (!$request->hasfile('file')) {
      return response()->json([
        'err' => 'must upload a file',
      ], Response::HTTP_BAD_REQUEST);
    }
    $formId = $request->input('formId');
    $validator = Validator::make([
      'studyId' => $studyId,
      'formId' => $formId,
    ], [
      'studyId' => 'required|exists:study,id',
      'formId' => 'nullable|exists:form,id',
    ]);

    $study = Study::find($studyId);
    $isTestStudy = $study->test_study_id === null;
    if (!$isTestStudy) {
      return response()->json([
        'err' => 'cannot import translations to non-test study',
      ], Response::HTTP_BAD_REQUEST);
    }
    if ($validator->fails()) {
      return response()->json([
        'msg' => $validator->errors()
      ], $validator->statusCode());
    }

    $source = $request->file('file');

    $ext = $source->getClientOriginalExtension();
    $name = $source->getClientOriginalName();
    Log::info("importing $name with extension $ext");
    $csvs = [];
    // TODO: if the file is a zip file, extract the csv files before importing
    if ($ext === 'zip') {
      // extract csvs from zip
      $zip = new \ZipArchive();
      $res = $zip->open($source->getRealPath());
      if ($res !== true) {
        return response()->json([
          'err' => 'could not open zip file',
        ], Response::HTTP_BAD_REQUEST);
      }
      for ($i = 0; $i < $zip->numFiles; $i++) {
        $csv = $zip->getFromIndex($i);
        array_push($csvs, $csv);
      }
      $zip->close();
    } else if ($ext === 'csv') {
      array_push($csvs, $source->get());
    } else {
      return response()->json([
        'err' => 'file must be a zip or csv file',
      ], Response::HTTP_BAD_REQUEST);
    }

    $csvCount = count($csvs);
    Log::info("importing $csvCount csv files");

    $locales = $this->studyLocales($studyId);

    // Read each csv file, validate the translations and form ids, and import all translations in a single transaction
    $formIds = [];
    $translationsByFormId = [];
    foreach($csvs as $csv) {
      $rows = CsvService::stringToAssociativeArrays($csv);
      if (empty($rows)) {
        return response()->json([
          'err' => 'csv file is empty',
        ], Response::HTTP_BAD_REQUEST);
      }

      if (empty($rows[0]['translation_id'])) {
        return response()->json([
          'err' => 'csv file must a translation_id column',
        ], Response::HTTP_BAD_REQUEST);
      }

      if (is_null($formId) && empty($rows[0]['form_id'])) {
        return response()->json([
          'err' => 'csv file must have a form_id column if formId is not provided',
        ], Response::HTTP_BAD_REQUEST);
      }
   
      foreach ($rows as $row) {
        $t = [
          'type' => $row['type'],
          'var_name' => $row['var_name'],
          'form_id' => $row['form_id'],
          'translation_id' => $row['translation_id'],
        ];
        foreach ($locales as $locale) {
          $t[$locale->language_name] = $row[$locale->language_name];
        }
        $formId = $row['form_id'] ?? $formId;
        if (!isset($translationsByFormId[$formId])) {
          $translationsByFormId[$formId] = [];
        }
        $formIds[$formId] = true;
        array_push($translationsByFormId[$formId], $t);
      }
    }

    if (!is_null($formId)) {
      $formIds[$formId] = true;
    }

    // check that all form ids are within the given study to prevent cross-study data corruption
    $studyFormsCount = StudyForm::where('study_id', $studyId)->whereIn('current_version_id', array_keys($formIds))->count();
    if ($studyFormsCount !== count($formIds)) {
      return response()->json([
        'err' => 'form_id not found in study',
      ], Response::HTTP_BAD_REQUEST);
    }

    // check that all translations match the form ids and check if any changed
    $changedTranslationText = [];
    $newTranslationText = [];
    foreach ($formIds as $formId => $_) {
      $form = $this->getForm($formId);
      $formTranslations = $this->formToTranslations($form, $locales);
      $formTranslationMap = [];
      foreach ($formTranslations as $t) {
        $formTranslationMap[$t['translation_id']] = $t;
      }
      if (!isset($translationsByFormId[$formId])) {
        return response()->json([
          'err' => "no translations found for form '$formId'",
        ], Response::HTTP_BAD_REQUEST);
      }
      $translations = $translationsByFormId[$formId];
      foreach ($translations as $t) {
        $tid = $t['translation_id'];
        $tFormId = isset($t['form_id']) ? $t['form_id'] : $formId;
        if ($tFormId === $formId && !isset($formTranslationMap[$tid])) {
          return response()->json([
            'err' => "translation '$tid' not found in this form",
          ], Response::HTTP_BAD_REQUEST);
        } else if ($tFormId !== $formId) {
          return response()->json([
            'err' => "translation '$tid' does not belong to form '$formId'",
          ], Response::HTTP_BAD_REQUEST);
        }
        $oldTranslation = $formTranslationMap[$tid];
        foreach ($locales as $locale) {
          $newText = $t[$locale->language_name];
          if (isset($oldTranslation[$locale->language_name]) && $oldTranslation[$locale->language_name]['translated_text'] !== $newText) {
            array_push($changedTranslationText, [
              'id' => $oldTranslation[$locale->language_name]['id'],
              'old_text' => $oldTranslation[$locale->language_name]['translated_text'],
              'translated_text' => $newText,
            ]);
          } else if (!isset($oldTranslation[$locale->language_name]) && $newText !== '') {
            array_push($newTranslationText, [
              'translation_id' => $tid,
              'locale_id' => $locale->id,
              'id' => Uuid::uuid4()->toString(),
              'translated_text' => $newText,
            ]);
          }
        }
      }
    }
    $nChanged = count($changedTranslationText);
    $nNew = count($newTranslationText);
    Log::info("updating translations: $nChanged changed, $nNew new");

    if (empty($changedTranslationText) && empty($newTranslationText)) {
      return response()->json([
        'msg' => 'no translations to update',
      ], Response::HTTP_OK);
    }

    // Actually update all translations
    DB::transaction(function () use ($changedTranslationText, $newTranslationText) {
      foreach ($changedTranslationText as $t) {
        TranslationText::where('id', $t['id'])->update([
          'translated_text' => $t['translated_text'],
        ]);
      }
      foreach ($newTranslationText as $t) {
        TranslationText::create($t);
      }
    });
    return response()->json([
      'msg' => 'translations updated',
      'updated' => count($changedTranslationText),
      'added' => count($newTranslationText),
    ], Response::HTTP_OK);
  }

  private function getForm (String $formId) {
    return Form::with(
      'sections',
      'nameTranslation',
      'sections.questionGroups',
      'sections.nameTranslation',
      'sections.formSections.repeatPromptTranslation'
    )->find($formId);
  }

  private function formTranslationsToCsv(Form $form, $locales): string {
    $headers = ['type', 'var_name'];
    foreach ($locales as $locale) {
      array_push($headers, $locale->language_name);
    }
    array_push($headers, 'form_id', 'translation_id');
    $translations = $this->formToTranslations($form, $locales);
    // modify the translations to be use strings instead of TranslationText models
    for($i = 0; $i < count($translations); $i++) {
      foreach ($locales as $locale) {
        if (isset($translations[$i][$locale->language_name])) {
          $translations[$i][$locale->language_name] = $translations[$i][$locale->language_name]->translated_text;
        }
      }
    }
    return CsvService::rowsToString($headers, $translations);
  }

  private function addTranslations (&$row, Translation $t, $locales) {
    foreach ($locales as $locale) {
      $tt = $t->translationText->first(function($t) use ($locale) {
        return $t->locale_id === $locale->id;
      });
      if ($tt) {
        $row[$locale->language_name] = $tt;
      } else {
        $row[$locale->language_name] = null;
      }
    }
  }

  private function studyLocales (String $studyId) {
    return Locale::whereIn('id', function($query) use ($studyId) {
      $query->select('locale_id')
        ->from('study_locale')
        ->where('study_id', $studyId);
    })->orderBy('language_name')->get();
  }

  private function formToTranslations (Form $form, $locales) {
    $form->sort();
    $translations = [];
    $row = [
      'translation_id' => $form->name_translation_id,
      'type' => 'form',
      'var_name' => '',
      'form_id' => $form->id,
    ];
    $this->addTranslations($row, $form->nameTranslation, $locales);
    array_push($translations, $row);
    foreach ($form->sections as $section) {
      $row = [
        'translation_id' => $section->name_translation_id,
        'type' => 'section',
        'var_name' => '',
        'form_id' => $form->id,
      ];
      $this->addTranslations($row, $section->nameTranslation, $locales);
      array_push($translations, $row);
      foreach ($section->questionGroups as $page) {
        foreach ($page->questions as $question) {
          $row = [
            'translation_id' => $question->question_translation_id,
            'type' => 'question',
            'var_name' => $question->var_name,
            'form_id' => $form->id,
          ];
          $this->addTranslations($row, $question->questionTranslation, $locales);
          array_push($translations, $row);
          foreach ($question->choices as $c) {
            $row = [
              'translation_id' => $c->choice_translation_id,
              'type' => 'choice',
              'var_name' => $question->var_name,
              'form_id' => $form->id,
            ];
            $this->addTranslations($row, $c->choiceTranslation, $locales);
            array_push($translations, $row);
          }
          // add label translations for slider question
          foreach ($question->sliderLabels() as $l) {
            $row = [
              'translation_id' => $l->id,
              'type' => 'slider_label',
              'var_name' => $question->var_name,
              'form_id' => $form->id,
            ];
            $this->addTranslations($row, $l, $locales);
            array_push($translations, $row);
          }
        }
      }
    }
    return $translations;
  }

  private function getFormFileName (Form $form, Locale $locale, $ext = 'csv', $suffix = '') {
    $localett = $form->nameTranslation->translationText->first(function($t) use ($locale) {
      return $t->locale_id === $locale->id;
    });
    if (!$localett) {
      $localett = $form->nameTranslation->translationText->first();
    }
    $formName = $localett->translated_text;
    return $formName . "_$locale->language_name$suffix.$ext";
  }

}
