<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SectionQuestionGroup extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'section_question_group';

    protected $fillable = [
        'id',
        'section_id',
        'question_group_id',
        'question_group_order',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function delete()
    {
        //\Log::info('SectionQuestionGroup->delete()');

        // Delete orphaned Question Groups
        if (SectionQuestionGroup::where('question_group_id', $this->question_group_id)->whereNull('deleted_at')->count() < 2) {
            $childQuestionGroups = QuestionGroup::where('id', $this->question_group_id)->get();
            foreach ($childQuestionGroups as $childQuestionGroup) {
                $childQuestionGroup->delete();
            }
        }

        return parent::delete();
    }
}
