<?php

namespace app\Library;

use App\Models\Translation;
use App\Models\TranslationText;
use Ramsey\Uuid\Uuid;
use DB;

class TranslationHelper
{
    public static function createNewTranslation($text, $localeId)
    {
        $translationTextId = Uuid::uuid4();
        $translationId = Uuid::uuid4();

        $newTranslationModel = new Translation;
        $newTranslationTextModel = new TranslationText;

        DB::transaction(function () use ($translationId, $translationTextId, $newTranslationModel, $newTranslationTextModel, $text, $localeId) {
            $newTranslationModel->id = $translationId;
            $newTranslationModel->save();

            $newTranslationTextModel->id = $translationTextId;
            $newTranslationTextModel->translation_id = $translationId;
            $newTranslationTextModel->locale_id = $localeId;
            $newTranslationTextModel->translated_text = $text;
            $newTranslationTextModel->save();
        });

        return $translationId;
    }

    public static function createNewTranslationFromTranslationTextArray($translationTextArray)
    {
        $translationId = Uuid::uuid4();

        $newTranslationModel = new Translation;

        DB::transaction(function () use ($translationId, $translationTextArray, $newTranslationModel) {
            $newTranslationModel->id = $translationId;
            $newTranslationModel->save();

            foreach ($translationTextArray as $translationText) {
                $translationTextId = Uuid::uuid4();
                $newTranslationTextModel = new TranslationText;
                $newTranslationTextModel->id = $translationTextId;
                $newTranslationTextModel->translation_id = $translationId;
                $newTranslationTextModel->locale_id = $translationText['localeId'];
                $newTranslationTextModel->translated_text = $translationText['translatedText'];
                $newTranslationTextModel->save();
            }
        });

        return $translationId;
    }

    public static function getTranslationText($translationId, $localeId)
    {
        $translationTextModel = TranslationText::where('translation_id', $translationId)
            ->where('locale_id', $localeId)
            ->get();

        return $translationTextModel->translated_text;
    }
}
