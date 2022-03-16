<?php

namespace App\Services;

use App\Models\Study;
use App\Models\Translation;
use App\Models\TranslationText;
use App\Models\Locale;
use Ramsey\Uuid\Uuid;

class TranslationService {
  public static function createNewTranslation() {
    $translationId = Uuid::uuid4();
    $translation = new Translation();

    $translation->id = $translationId;

    $translation->save();

    return $translationId;
  }

  static function createTranslationForDefault(String $text, Study $study): Translation {
    $translation = new Translation;
    $translation->id = Uuid::uuid4();
    $translation->save();
    $translationText = new TranslationText;
    $translationText->id = Uuid::uuid4();
    $translationText->translation_id = $translation->id;
    $translationText->translated_text = $text;
    $translationText->locale_id = $study->default_locale_id;
    $translationText->save();
    return $translation;
  }

  public static function importTranslation($translationObject) {
    $translationId = Uuid::uuid4();
    $translation = new Translation();
    $translation->id = $translationId;
    $translation->save();

    foreach ($translationObject['translation_text'] as $translationTextObject) {
      $translatedText = $translationTextObject['translated_text'];
      $localeId = $translationTextObject['locale_id'];
      TranslationTextService::createTranslationText($translationId, $translatedText, $localeId);
    }

    return $translationId;
  }

  /**
   * Create a translation from an array with definitions like "geo.translation.en" and "respondent.translation.es"
   */
  public static function createTranslationFromArray(Array $row, String $prefix = ''): Translation {
    $translations = [];
    foreach ($row as $key => $item) {
      $key = str_replace($prefix, '', $key);
      if (preg_match('/translation\.*/', $key) === 1) {
        $languageCode = str_replace('translation.', '', $key);
        $translations[$languageCode] = $item;
      }
    }
    if (count($translations) === 0) {
      throw new \Exception("No translations found in array");
    }
    $translation = Translation::create([
      'id' => Uuid::uuid4(),
    ]);

    // Create a translation_text for each locale supplied
    foreach ($translations as $code => $text) {
      $locale = Locale::where('language_tag', $code)
        ->orWhere('language_name', 'like', $code)
        ->first();
      if (!isset($locale)) {
        throw new \Exception("No locale for '$code' found");
      }
      $text = TranslationText::create([
        'id' => Uuid::uuid4(),
        'locale_id' => $locale->id,
        'translated_text' => $text,
        'translation_id' => $translation->id,
      ]);
    }
    return $translation;
  }

  static public function copyTranslation (Translation $t): Translation {
    $newTranslation = Translation::create([
      'id' => Uuid::uuid4()
    ]);
    $newTranslation->save();
    foreach ($t->translationText as $tt) {
      $ntt = $tt->replicate()->fill([
        'id' => Uuid::uuid4(),
        'translation_id' => $newTranslation->id,
      ]);
      $ntt->save();
    }
    return $newTranslation;
  }
  
}
