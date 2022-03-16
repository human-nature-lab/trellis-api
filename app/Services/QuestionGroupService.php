<?php

namespace app\Services;

use App\Models\QuestionGroup;
use App\Models\Section;
use App\Models\SectionQuestionGroup;
use App\Services\QuestionService;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\DB;

class QuestionGroupService {
  public static function getAllQuestionGroups() {
    $questionGroups = QuestionGroup::get();

    return $questionGroups;
  }

  public static function createQuestionGroup($sectionId, $sortOrder = -1) {
    $questionGroupId = Uuid::uuid4();
    $sectionQuestionGroupId = Uuid::uuid4();

    $questionGroupModel = new QuestionGroup;

    DB::transaction(function () use ($questionGroupId, $sectionQuestionGroupId, $questionGroupModel, $sectionId, $sortOrder) {
      $questionGroupModel->id = $questionGroupId;
      $questionGroupModel->save();
      $questionGroupModel->section_id = $sectionId;

      $sectionQuestionGroupModel = new SectionQuestionGroup;
      $sectionQuestionGroupModel->id = $sectionQuestionGroupId;
      $sectionQuestionGroupModel->section_id = $sectionId;
      $sectionQuestionGroupModel->question_group_id = $questionGroupId;
      if ($sortOrder < 0) {
        $maxQuestionGroupOrder = DB::table('section_question_group')
          ->where('section_id', '=', $sectionId)
          ->whereNull('deleted_at')
          ->max('question_group_order');

        if ($maxQuestionGroupOrder == null) {
          $maxQuestionGroupOrder = 0;
        }

        $sortOrder = $maxQuestionGroupOrder + 1;
      }
      $sectionQuestionGroupModel->question_group_order = $sortOrder;
      $sectionQuestionGroupModel->save();
    });

    $returnQuestionGroup = Section::find($sectionId)
      ->questionGroups()
      ->find($questionGroupId);

    return $returnQuestionGroup;
  }

  public static function copyQuestionGroup(QuestionGroup $qg, Array &$questionMap): QuestionGroup {
    $qg = $qg->replicate(['id', 'questions'])->fill([
      'id' => Uuid::uuid4(),
    ]);
    $qg->save();
    foreach ($qg->questions as $question) {
      $q = QuestionService::copyQuestion($question);
      $questionMap[$question->id] = $q->id;
      $q->question_group_id = $qg->id;
      $q->save();
    }
    foreach ($qg->skips as $skip) {
      $s = $skip->replicate()->fill([
        'id' => Uuid::uuid4(),
      ]);
      $qgs = $skip->pivot->replicate()->fill([
        'id' => Uuid::uuid4(),
        'skip_id' => $s->id,
        'question_group_id' => $qg->id,
      ]);
      $s->save();
      $qgs->save();
      foreach ($s->conditions as $cond) {
        $c = $cond->replicate()->fill([
          'id' => Uuid::uuid4(),
          'skip_id' => $s->id,
        ]);
        $c->save();
      }
    }
    return $qg;
  }
}
