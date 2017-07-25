<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class FormSection extends Model {

	use SoftDeletes;

	public $incrementing = false;

	protected $table = 'form_section';

	protected $with = ['repeatPromptTranslation'];

	protected $fillable = [
		'id',
		'created_at',
		'updated_at',
		'deleted_at',
		'form_id',
		'section_id',
		'sort_order',
		'is_repeatable',
		'max_repetitions',
		'repeat_prompt_translation_id'
	];

    public function repeatPromptTranslation() {
        return $this
            ->belongsTo('App\Models\Translation', 'repeat_prompt_translation_id')
            ->with('translationText');
    }

    public function delete() {
        //Log::info('FormSection->delete()');

        // Delete orphaned Sections
        // This causes an infinite loop
        //$formSectionCount = FormSection::where('section_id', '=', $this->section_id)->whereNull('deleted_at')->count();

        //if ($formSectionCount < 2) {
        //    Section::where('id', '=', $this->section_id)->first()->delete();
        //}

        return parent::delete();
    }
}