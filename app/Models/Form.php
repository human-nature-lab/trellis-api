<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Form extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'form';

    protected $casts = ['is_published' => 'string'];

    protected $fillable = [
        'id',
        'form_master_id',
        'name_translation_id',
        'version',
        'is_published',
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

    public function sections()
    {
        return $this
            ->belongsToMany('App\Models\Section', 'form_section')
            ->whereNull('form_section.deleted_at')
            ->withPivot('sort_order', 'is_repeatable', 'max_repetitions', 'repeat_prompt_translation_id')
            ->withTimestamps()
            ->with('questionGroups', 'nameTranslation', 'formSections.repeatPromptTranslation');
    }

    public function skips()
    {
        return $this
            ->belongsToMany('App\Models\Skip', 'form_skip')
            ->whereNull('form_skip.deleted_at')
            ->withPivot('form_id')
            ->withTimestamps()
            ->with('conditions');
    }

    /*
    public function delete()
    {
        //\Log::info("Form->delete()");
        $childFormSections = FormSection::where('form_id', '=', $this->id)->get();
        foreach ($childFormSections as $childFormSection) {
            $childFormSection->delete();
        }

        $childSurveys = Survey::where('form_id', '=', $this->id)->get();
        foreach ($childSurveys as $childSurvey) {
            $childSurvey->delete();
        }

        $studyForms = StudyForm::where('form_master_id', '=', $this->form_master_id)->get();
        foreach ($studyForms as $studyForm) {
            $studyForm->delete();
        }

        return parent::delete();
    }
    */
}
