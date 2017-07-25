<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

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

    public function formSections() {
        return $this
            ->hasMany('App\Models\FormSection');

    }

    public function questionGroups() {
        return $this
            ->belongsToMany('App\Models\QuestionGroup', 'section_question_group')
            ->whereNull('section_question_group.deleted_at')
            ->withPivot('section_id', 'question_group_order')
            ->withTimestamps()
            ->with('questions', 'skips');
    }

    public function delete() {
        Log::Debug("Got here.");
        $childSectionQuestionGroups = SectionQuestionGroup::where('section_id', '=', $this->id)->get();
        foreach ($childSectionQuestionGroups as $childSectionQuestionGroup) {
            $childSectionQuestionGroup->delete();
        }

        $formSections = FormSection::where('section_id', '=', $this->id)->get();
        foreach ($formSections as $formSection) {
            $formSection->delete();
        }

        return parent::delete();
    }
}
