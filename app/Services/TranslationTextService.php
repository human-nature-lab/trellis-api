<?php

namespace app\Services;

use App\Models\TranslationText;
use Ramsey\Uuid\Uuid;

class TranslationTextService
{
    public static function addNewTranslationText($request, $translationId, $localeId)
    {
        $translationText = new TranslationText();

        $translationText->id = Uuid::uuid4();
        $translationText->translation_id = $translationId;
        $translationText->translated_text = $request->input('question_text');
        $translationText->locale_id = $localeId;

        $translationText->save();

        return $translationText;
    }

    public static function createTranslationText($translationId, $translatedText, $localeId)
    {
        $translationText = new TranslationText();

        $translationText->id = Uuid::uuid4();
        $translationText->translation_id = $translationId;
        $translationText->translated_text = $translatedText;
        $translationText->locale_id = $localeId;

        $translationText->save();

        return $translationText;
    }
    
    
}
