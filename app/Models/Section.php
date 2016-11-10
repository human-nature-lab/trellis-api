<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
	use SoftDeletes;

	public $incrementing = false;

	protected $table = 'section';

	protected $fillable = [
		'id',
		'created_at',
		'updated_at',
		'deleted_at',
		'name_translation_id',
	];

    public function nameTranslation() {
        return $this
            ->belongsTo('App\Models\Translation', 'name_translation_id')
            ->with('translationText');
    }

    public function questionGroups() {
        return $this
            ->belongsToMany('App\Models\QuestionGroup', 'section_question_group')
            ->whereNull('section_question_group.deleted_at')
            ->withTimestamps()
            ->with('questions');
    }
}
