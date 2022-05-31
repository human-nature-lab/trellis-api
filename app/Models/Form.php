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
        return $this->hasMany('App\Models\StudyForm', 'current_version_id', 'id')
            ->whereNull('study_form.deleted_at')
            ->with('type');
    }

    public function sections()
    {
        return $this
            ->belongsToMany('App\Models\Section', 'form_section')
            ->using('App\Models\FormSection')
            ->withPivot('id', 'sort_order', 'is_repeatable', 'max_repetitions', 'repeat_prompt_translation_id', 'randomize_follow_up')
            ->whereNull('form_section.deleted_at')
            ->withTimestamps()
            // ->with('questionGroups', 'nameTranslation', 'formSections.repeatPromptTranslation')
            ->orderBy('form_section.sort_order');
    }

    public function skips()
    {
        return $this
            ->belongsToMany('App\Models\Skip', 'form_skip')
            ->using('App\Models\FormSkip')
            ->withPivot('form_id')
            ->whereNull('form_skip.deleted_at')
            ->withTimestamps()
            ->with('conditions')
            ->orderBy('precedence');
    }
    
    public function versions () {
      return $this->hasMany('App\Models\Form', 'form_master_id', 'form_master_id');
    }

}
