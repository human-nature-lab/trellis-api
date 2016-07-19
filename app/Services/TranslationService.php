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
}