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

    public function studyForm() {
        return $this->hasMany('App\Models\StudyForm', 'form_master_id')
            ->whereNull('study_form.deleted_at')
            ->with('type');
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

}
