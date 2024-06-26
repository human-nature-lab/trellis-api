<?php

namespace app\Services;

use App\Services\TranslationService;
use App\Services\TranslationTextService;
use App\Services\QuestionChoiceService;
use App\Services\QuestionParameterService;
use App\Models\Question;
use App\Models\QuestionChoice;
use App\Models\Translation;
use App\Models\TranslationText;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestionService
{
    public static function getAllQuestionsPaginated($perPage, $studyId)
    {
        $questions = Question::join('section_question_group AS sqg', 'question.question_group_id', '=', 'sqg.question_group_id')
            ->join('form_section AS fs', 'sqg.id', '=', 'fs.section_id')
            ->join('form AS f', 'fs.form_id', '=', 'f.id')
            ->join('study_form AS sf', 'f.form_master_id', '=', 'sf.form_master_id')
            ->join('study AS s', 'sf.study_id', '=', 's.id')
            ->where('s.id', $studyId)
            ->paginate($perPage);

        return $questions;
    }

    public static function addQuestion($request)
    {
        $translationService = new TranslationService();
        $translationTextService = new TranslationTextService();
        $question = new Question;

        $translationId = $translationService->createNewTranslation();
        $translationText = $translationTextService->addNewTranslationText($request, $translationId, $request->input('locale_id'));

        $question->id = Uuid::uuid4();
        $question->question_type_id = $request->input('question_type');
        $question->question_translation_id = $translationId;
        $question->question_group_id = $request->input('question_group');
        $question->sort_order = 0;
        $question->var_name = $request->input('var_name');

        $question->save();

        return $question;
    }

    public static function createTranslatedQuestion($questionGroupId, $questionTranslationId, $varName, $questionTypeId, $sortOrder) {
        $newQuestionModel = new Question;
        $questionId = Uuid::uuid4();

        DB::transaction(function () use ($questionId, $questionTranslationId, $questionTypeId, $questionGroupId, $varName, $newQuestionModel, $sortOrder) {
            $newQuestionModel->id = $questionId;
            $newQuestionModel->question_type_id = $questionTypeId;
            $newQuestionModel->question_translation_id = $questionTranslationId;
            $newQuestionModel->question_group_id = $questionGroupId;
            $newQuestionModel->sort_order = $sortOrder;
            $newQuestionModel->var_name = $varName;
            $newQuestionModel->save();
        });

        $returnQuestion = Question::with('choices', 'questionTranslation', 'questionType', 'questionParameters', 'assignConditionTags')
            ->find($questionId);

        return $returnQuestion;
    }

    public function createQuestion($questionText, $localeId, $questionTypeId, $questionGroupId, $varName)
    {
        // TODO: handle error when locale tag is not found.
        $localeTag = DB::table('locale')->where('id', '=', $localeId)->first()->language_tag;

        $textLocaleArray = array(
            $localeTag => $questionText
        );

        $returnQuestion = QuestionService::createQuestionLocalized($textLocaleArray, $questionTypeId, $questionGroupId, $varName);

        return $returnQuestion;
    }

    static public function copyQuestion (Question $question): Question {
      $t = TranslationService::copyTranslation($question->questionTranslation);
      $q = $question->replicate(['id', 'question_translation_id'])->fill([
        'id'=> Uuid::uuid4(),
        'question_translation_id' => $t->id,
      ]);
      $q->save();
      
      foreach($question->questionParameters as $p) {
        $p = QuestionParameterService::copyQuestionParameter($p);
        $p->question_id = $q->id;
        $p->save();
      }

      foreach($question->assignConditionTags as $act) {
        $a = $act->replicate(['id'])->fill([
          'id' => Uuid::uuid4(),
        ]);
        $a->save();
        $qa = $act->pivot->replicate(['id', 'question_id', 'assign_condition_tag_id'])->fill([
          'id' => Uuid::uuid4(),
          'question_id' => $q->id,
          'assign_condition_tag_id' => $a->id,
        ]);
        $qa->save();
      }

      foreach($question->choices as $c) {
        $c = QuestionChoiceService::copyChoice($c);
        $qc = $c->pivot->replicate(['id', 'choice_id', 'question_id'])->fill([
          'id' => Uuid::uuid4(),
          'choice_id' => $c->id,
          'question_id' => $q->id,
        ]);
        $c->save();
        $qc->save();
      }

      foreach($question->preloadActions as $p) {
        $pa = $p->replicate(['id', 'question_id'])->fill([
          'id' => Uuid::uuid4(),
          'question_id' => $q->id,
        ]);
        $pa->save();
      }

      return $q;
    }

    public static function createQuestionLocalized($textLocaleArray, $questionTypeId, $questionGroupId, $varName)
    {
        $newQuestionModel = new Question;
        $questionId = Uuid::uuid4();

        DB::transaction(function () use ($questionId, $textLocaleArray, $questionTypeId, $questionGroupId, $varName, $newQuestionModel) {
            $translationId = Uuid::uuid4();
            $newTranslationModel = new Translation;
            $newTranslationModel->id = $translationId;
            $newTranslationModel->save();

            foreach ($textLocaleArray as $localeTag => $translationText) {
                $translationTextId = Uuid::uuid4();

                $newTranslationTextModel = new TranslationText;

                $newTranslationTextModel->id = $translationTextId;
                $newTranslationTextModel->translation_id = $translationId;
                Log::info('$localeTag: ' . $localeTag);
                Log::info('$translationText: ' . $translationText);
                $newTranslationTextModel->locale_id = DB::table('locale')->where('language_tag', '=', $localeTag)->first()->id;

                $newTranslationTextModel->translated_text = $translationText;
                $newTranslationTextModel->save();
            }

            $newQuestionModel->id = $questionId;
            $newQuestionModel->question_type_id = $questionTypeId;
            $newQuestionModel->question_translation_id = $translationId;
            $newQuestionModel->question_group_id = $questionGroupId;

            $maxSortOrder = DB::table('question')
                ->where('question_group_id', '=', $questionGroupId)
                ->whereNull('deleted_at')
                ->max('sort_order');

            $newQuestionModel->sort_order = $maxSortOrder + 1;
            $newQuestionModel->var_name = $varName;
            $newQuestionModel->save();
        });

        $returnQuestion = Question::with('choices', 'questionTranslation', 'questionType', 'questionParameters', 'assignConditionTags')
            ->find($questionId);

        return $returnQuestion;
    }
}
