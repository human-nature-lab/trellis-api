<?php

namespace app\Services;

use App\Services\TranslationService;
use App\Services\TranslationTextService;
use App\Models\Choice;
use App\Models\QuestionChoice;
use App\Models\Translation;
use App\Models\TranslationText;
use Ramsey\Uuid\Uuid;
use DB;

class QuestionChoiceService
{

    public function createQuestionChoice($val, $text, $sortOrder, $localeId, $questionId) {

        $localeTag = DB::table('locale')->where('id', '=', $localeId)->locale_tag;

        $textLocaleArray = Array(
            $localeTag => $text
        );

        $newQuestionChoiceModel = createQuestionChoiceLocalized($val, $textLocaleArray, $sortOrder, $questionId);

        return $newQuestionChoiceModel;
    }

    public function createQuestionChoiceLocalized($val, $textLocaleArray, $sortOrder, $questionId) {
        $questionChoiceId = Uuid::uuid4();
        $choiceId = Uuid::uuid4();
        $translationId = Uuid::uuid4();
        $translationTextId = Uuid::uuid4();

        $newQuestionChoiceModel = new QuestionChoice;

        DB::transaction(function() use($val, $textLocaleArray, $sortOrder, $questionId, $questionChoiceId, $choiceId, $translationId, $translationTextId, $newQuestionChoiceModel) {

            $newTranslationModel = new Translation;
            $newTranslationModel->id = $translationId;
            $newTranslationModel->save();

            // TODO: move this function to TranslationTextService
            foreach ($textLocaleArray as $localeTag => $translationText) {
                $translationTextId = Uuid::uuid4();

                $newTranslationTextModel = new TranslationText;

                $newTranslationTextModel->id = $translationTextId;
                $newTranslationTextModel->translation_id = $translationId;
                $newTranslationTextModel->locale_id = DB::table('locale')->where('language_tag', '=', $localeTag)->first()->id;

                $newTranslationTextModel->translated_text = $translationText;
                $newTranslationTextModel->save();
            }

            $newChoiceModel = new Choice;
            $newChoiceModel->id = $choiceId;
            $newChoiceModel->choice_translation_id = $translationId;
            $newChoiceModel->val = $val;
            $newChoiceModel->save();

            $newQuestionChoiceModel->id = $questionChoiceId;
            $newQuestionChoiceModel->question_id = $questionId;
            $newQuestionChoiceModel->choice_id = $choiceId;
            $newQuestionChoiceModel->sort_order = $sortOrder;
            $newQuestionChoiceModel->save();

            // TODO: Should probably remove this 'text' field
            $newQuestionChoiceModel->text = '';
            $newQuestionChoiceModel->val = $val;
        });

        return $newQuestionChoiceModel;
    }
}