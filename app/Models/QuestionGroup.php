<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionGroup extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'question_group';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /*
    public function delete()
    {
        //\Log::info('QuestionGroup->delete()');

        $childQuestions = Question::where("question_group_id", $this->id)->get();
        foreach ($childQuestions as $childQuestion) {
            $childQuestion->delete();
        }

        SectionQuestionGroup::where("question_group_id", $this->id)->delete();

        return parent::delete();
    }
    */

    public function questions()
    {
        return $this
            ->hasMany('App\Models\Question')
            ->with('choices', 'questionTranslation', 'questionType', 'questionParameters', 'assignConditionTags');
    }

    public function skips()
    {
        return $this
            ->belongsToMany('App\Models\Skip', 'question_group_skip')
            ->whereNull('question_group_skip.deleted_at')
            ->withPivot('question_group_id')
            ->withTimestamps()
            ->with('conditions');
    }
}
