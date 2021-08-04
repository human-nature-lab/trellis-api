<?php

namespace App\Services;

use App\Models\Study;
use App\Models\Translation;
use App\Models\TranslationText;
use Ramsey\Uuid\Uuid;

class TranslationService
{
    public static function createNewTranslation () {
        $translationId = Uuid::uuid4();
        $translation = new Translation();

        $translation->id = $translationId;

        $translation->save();

        return $translationId;
    }

    static function createTranslationForDefault (String $text, Study $study): Translation {
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

    public static function importTranslation($translationObject)
    {
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

  public function copyTranslation (Translation $t): Translation {
    $newTranslation = Translation::create([
      'id' => Uuid::uuid4()
    ]);
    $translations = [];
    foreach ($t->translationText as $tt) {
      $translations[] = new TranslationText([
        'id' => Uuid::uuid4(),
        'translation_id' => $newTranslation->id,
        'translated_text' => $tt->translated_text,
        'locale_id' => $tt->locale_id,
      ]);
    }
    $newTranslation->translationText()->insertMany($translations);
    return $newTranslation;
  }
}
