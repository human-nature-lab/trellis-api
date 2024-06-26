<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Study extends Model {
  use SoftDeletes;

  public $incrementing = false;

  protected $table = 'study';

  protected $fillable = [
    'id',
    'name',
    'photo_quality',
    'default_locale_id',
    'created_at',
    'updated_at',
    'deleted_at',
    'test_study_id',
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

  public function parameters() {
    return $this
      ->hasMany('App\Models\StudyParameter', 'study_id')
      ->with('parameter');
  }

  public function defaultLocale() {
    return $this
      ->hasOne('App\Models\Locale', 'id', 'default_locale_id');
  }

  public function forms() {
    return $this
      ->belongsToMany('App\Models\Form', 'study_form', 'study_id', 'form_master_id')
      ->using('App\Models\StudyForm')
      ->withPivot('id', 'sort_order', 'form_type_id', 'census_type_id', 'geo_type_id')
      ->whereNull('study_form.deleted_at')
      ->withTimestamps()
      ->with('nameTranslation', 'skips');
  }

  public function geoTypes() {
    return $this->hasMany('App\Models\GeoType');
  }

  public function testStudy() {
    return $this->hasOne('App\Models\Study', 'id', 'test_study_id');
  }

  public function isProd(): bool {
    return isset($this->test_study_id);
  }
          // Use the test study if we're trying to access a prod study

  /*
    public function delete()
    {
        StudyForm::where('study_id', $this->id)
            ->delete();

        StudyLocale::where('study_id', $this->id)
            ->delete();

        StudyRespondent::where('study_id', $this->id)
            ->delete();

        UserStudy::where('study_id', $this->id)
            ->delete();

        return parent::delete();
    }
    */
}
