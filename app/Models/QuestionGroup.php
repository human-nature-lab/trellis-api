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
			'deleted_at',
	];

    public function delete() {
        Question::where("question_group_id", $this->id)->delete();
        SectionQuestionGroup::where("question_group_id", $this->id)->delete();

        return parent::delete();
    }

    public function questions() {
        return $this
            ->hasMany('App\Models\Question')
            ->with('choices', 'questionTranslation', 'questionType', 'questionParameters');
    }

}
