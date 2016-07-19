<?php

namespace app\Services;

use App\Services\TranslationService;
use App\Services\TranslationTextService;
use App\Models\Question;
use Ramsey\Uuid\Uuid;

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

    public static function addQuestionHtml($request)
}