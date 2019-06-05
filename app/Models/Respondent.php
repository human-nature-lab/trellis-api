<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Respondent extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'respondent';

    protected $fillable = [
        'id',
        'assigned_id',
        'geo_id',
        'notes',
        'geo_notes',
        'name',
        'associated_respondent_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function geos () {
        return $this->belongsToMany('App\Models\Geo', 'respondent_geo')
            ->using('App\Models\RespondentGeo')
            ->withPivot('is_current', 'notes', 'id', 'previous_respondent_geo_id')
            ->whereNull('respondent_geo.deleted_at')
            ->with('geoType', 'nameTranslation', 'photos');
    }

    public function rGeos () {
        return $this->hasMany('App\Models\RespondentGeo')
            ->with('geo');
    }

    public function names () {
        return $this->hasMany('App\Models\RespondentName');
    }

    public function photos()
    {
        return $this
            ->belongsToMany('App\Models\Photo', 'respondent_photo')
            ->whereNull('respondent_photo.deleted_at')
            ->where('respondent_photo.sort_order', '=', '0')
            ->withTimestamps();
    }

    public function studies()
    {
        return $this->belongsToMany('App\Models\Study', 'study_respondent')
            ->whereNull('study_respondent.deleted_at')
            ->withTimestamps();
    }

    public function respondentConditionTags () {
        return $this->belongsToMany('App\Models\ConditionTag', 'respondent_condition_tag')
            ->using('App\Models\RespondentConditionTag')
            ->withPivot('id')
            ->whereNull('respondent_condition_tag.deleted_at')
            ->withTimestamps();
    }

    public function currentGeo () {
        return $this->hasOne('App\Models\RespondentGeo')
            ->whereNull('respondent_geo.deleted_at')
            ->where('respondent_geo.is_current', '=', 1)
            ->with('geo');
    }

    public function name () {
        return $this->hasMany('App\Models\RespondentName')
            ->where('respondent_name.is_display_name', '=', 1);
    }


    /*
    public function delete()
    {
        StudyRespondent::where('respondent_id', $this->id)
            ->delete();

        RespondentPhoto::where('respondent_id', $this->id)
            ->delete();

        RespondentConditionTag::where('respondent_id', $this->id)
            ->delete();

        RespondentGroupTag::where('respondent_id', $this->id)
            ->delete();

        return parent::delete();
    }
    */
}
