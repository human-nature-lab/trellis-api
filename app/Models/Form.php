<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Form extends Model {

    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'form';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'form_master_id',
        'name_translation_id',
        'version'
    ];

    public function nameTranslation() {
        return $this
            ->belongsTo('App\Models\Translation', 'name_translation_id')
            ->with('translationText');
    }

    public function sections() {
        return $this
            ->belongsToMany('App\Models\Section', 'form_section')
            ->whereNull('form_section.deleted_at')
            ->withPivot('sort_order', 'is_repeatable', 'max_repetitions', 'repeat_prompt_translation_id')
            ->withTimestamps()
            ->with('questionGroups', 'nameTranslation');
    }

    public function delete() {
        //Log::info("Form->delete()");
        $childFormSections = FormSection::where('form_id', '=', $this->id)->get();
        foreach ($childFormSections as $childFormSection) {
            $childFormSection->delete();
        }

        $childSurveys = Survey::where('form_id', '=', $this->id)->get();
        foreach ($childSurveys as $childSurvey) {
            $childSurvey->delete();
        }

        return parent::delete();
    }

}