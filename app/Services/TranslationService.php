<?php

namespace App\Services;

use App\Models\Translation;
use Ramsey\Uuid\Uuid;

class TranslationService
{
    public static function createNewTranslation()
    {
        $translationId = Uuid::uuid4();
        $translation = new Translation();

        $translation->id = $translationId;

        $translation->save();

        return $translationId;
    }

    public static function importTranslation($translationObject, TranslationTextService $translationTextService)
    {
        $translationId = Uuid::uuid4();
        $translation = new Translation();
        $translation->id = $translationId;
        $translation->save();

        foreach ($translationObject['translation_text'] as $translationTextObject) {
            $translatedText = $translationTextObject['translated_text'];
            $localeId = $translationTextObject['locale_id'];
            $translationTextService->createTranslationText($translationId, $translatedText, $localeId);
        }

        return $translationId;
    }
}
