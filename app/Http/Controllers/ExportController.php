<?php

namespace App\Http\Controllers;

use App\Services\MarkdownService;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use App\Models\Form;
use App\Models\Locale;
use App\Models\Study;
use App\Models\Translation;
use App\Services\MarkdownConfig;
use App\Services\CsvService;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    $study = Study::find($studyId);
    $locales = Locale::whereIn('id', function($query) use ($studyId) {
      $query->select('locale_id')
        ->from('study_locale')
        ->where('study_id', $studyId);
    })->get();

    $mainLocale = $locales->first(function($locale) use ($study) {
      return $locale->id === $study->default_locale_id;
    });
    return new StreamedResponse(function() use ($formIds, $locales, $mainLocale) {
      $zip = new \ZipStream\ZipStream('trellis_translations.zip');
      foreach ($formIds as $formId) {
        $form = Form::with(
          'sections',
          'nameTranslation',
          'sections.questionGroups',
          'sections.nameTranslation',
          'sections.formSections.repeatPromptTranslation'
        )->find($formId);
        $csv = $this->formTranslationsToCsv($form, $locales);
        $zip->addFile($this->getFormFileName($form, $mainLocale, 'csv'), $csv);
      }
      $zip->finish();
    });
  }

  private function formTranslationsToCsv(Form $form, $locales): string {
    $translations = [];
    $form->sort();
    $headers = ['type', 'var_name'];
    foreach ($locales as $locale) {
      array_push($headers, $locale->language_name);
    }
    array_push($headers, 'form_id', 'translation_id');
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
    return CsvService::rowsToString($headers, $translations);
  }

  private function addTranslations (&$row, Translation $t, $locales) {
    foreach ($locales as $locale) {
      $tt = $t->translationText->first(function($t) use ($locale) {
        return $t->locale_id === $locale->id;
      });
      if ($tt) {
        $row[$locale->language_name] = $tt->translated_text;
      } else {
        $row[$locale->language_name] = '';
      }
    }
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
