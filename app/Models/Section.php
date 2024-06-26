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
        'name_translation_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function nameTranslation()
    {
        return $this
            ->belongsTo('App\Models\Translation', 'name_translation_id')
            ->with('translationText');
    }

    public function formSections()
    {
        return $this
            ->hasMany('App\Models\FormSection');
    }

    public function questionGroups()
    {
        return $this
            ->belongsToMany('App\Models\QuestionGroup', 'section_question_group')
            ->using('App\Models\SectionQuestionGroup')
            ->withPivot('id', 'section_id', 'question_group_order')
            ->whereNull('section_question_group.deleted_at')
            ->withTimestamps()
            ->with('questions', 'skips')
            ->orderBy('section_question_group.question_group_order');
    }

    /*
    public function delete()
    {
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
    */
}
