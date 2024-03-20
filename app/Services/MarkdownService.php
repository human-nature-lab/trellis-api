<?php

namespace App\Services;

use App\Models\Form;
use App\Models\FormSection;
use App\Models\StudyForm;
use App\Models\Study;
use App\Models\TranslationText;
use App\Models\Translation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Throwable;

class MarkdownConfig {
  public $localeId;
}

class MarkdownService {

  public function formToMarkdown(Form $form, MarkdownConfig $config): string {
    $markdown = '';
    $markdown .= $this->formHeader($form, $config);
    $markdown .= $this->formQuestions($form, $config);
    return $markdown;
  }

  private function formHeader(Form $form, MarkdownConfig $config): string {
    $markdown = '';
    $markdown .= "# " . TranslationService::getTranslated($form->nameTranslation, $config->localeId) . "\n";
    $markdown .= $form->description . "\n";
    return $markdown;
  }

  private function formQuestions(Form $form, MarkdownConfig $config): string {
    return 'TODO: questions';
  }

}