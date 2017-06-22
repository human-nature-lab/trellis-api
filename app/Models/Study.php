<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Study extends Model
{
	use SoftDeletes;

	public $incrementing = false;

	protected $table = 'study';

	protected $fillable = [
		'id',
		'created_at',
		'updated_at',
		'deleted_at',
		'name',
		'photo_quality',
		'root_geo',
		'census_form_master_id',
		'default_locale_id'
	];

	public function users() {
		return $this->belongsToMany('App\Models\User', 'user_study')
		->whereNull('user_study.deleted_at')
		->withTimestamps();
	}

	public function respondents() {
		return $this->belongsToMany('App\Models\Respondent', 'study_respondent')
		->whereNull('study_respondent.deleted_at')
		->withTimestamps();
	}

    public function locales() {
        return $this
        ->belongsToMany('App\Models\Locale', 'study_locale')
        ->whereNull('study_locale.deleted_at')
        ->withTimestamps();
    }

    public function forms() {
	    return $this
        ->belongsToMany('App\Models\Form', 'study_form', 'study_id', 'form_master_id')
        ->whereNull('study_form.deleted_at')
        ->withPivot('sort_order', 'form_type')
        ->withTimestamps()
        ->with('nameTranslation');
    }

    public function delete() {
        // TODO: Soft delete all child elements
        return parent::delete();
    }

}
