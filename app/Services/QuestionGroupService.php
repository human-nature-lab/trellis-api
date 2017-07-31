<?php

namespace app\Services;

use App\Models\QuestionGroup;
use App\Models\Section;
use App\Models\SectionQuestionGroup;
use Ramsey\Uuid\Uuid;
use DB;

class QuestionGroupService
{
    public static function getAllQuestionGroups()
    {
        $questionGroups = QuestionGroup::get();

        return $questionGroups;
    }

    public function createQuestionGroup($sectionId)
    {
        $questionGroupId = Uuid::uuid4();
        $sectionQuestionGroupId = Uuid::uuid4();

        $questionGroupModel = new QuestionGroup;

        DB::transaction(function () use ($questionGroupId, $sectionQuestionGroupId, $questionGroupModel, $sectionId) {
            $questionGroupModel->id = $questionGroupId;
            $questionGroupModel->save();
            $questionGroupModel->section_id = $sectionId;

            $sectionQuestionGroupModel = new SectionQuestionGroup;
            $sectionQuestionGroupModel->id = $sectionQuestionGroupId;
            $sectionQuestionGroupModel->section_id = $sectionId;
            $sectionQuestionGroupModel->question_group_id = $questionGroupId;
            $maxQuestionGroupOrder = DB::table('section_question_group')
                ->where('section_id', '=', $sectionId)
                ->whereNull('deleted_at')
                ->max('question_group_order');

            //$sectionQuestionGroupModel->question_group_order = 1;
            $sectionQuestionGroupModel->question_group_order = $maxQuestionGroupOrder + 1;
            $sectionQuestionGroupModel->save();
        });

        $returnQuestionGroup = Section::find($sectionId)
            ->questionGroups()
            ->find($questionGroupId);

        return $returnQuestionGroup;
    }
}
