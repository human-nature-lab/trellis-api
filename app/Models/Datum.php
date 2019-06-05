<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Datum extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'datum';

    public $fillable = [
        'choice_id',
        'datum_type_id',
        'edge_id',
        'event_order',
        'geo_id',
        'id',
        'name',
        'photo_id',
        'question_datum_id',
        'respondent_geo_id',
        'respondent_name_id',
        'roster_id',
        'sort_order',
        'survey_id',
        'val',
        'random_sort_order',
        'action_id',

        'created_at',
        'deleted_at',
        'updated_at'
    ];

    public $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function choice () {
        return $this->hasOne('App\Models\Choice', 'id', 'choice_id')
            ->whereNull('choice.deleted_at')
            ->with('choiceTranslation');
    }

    public function geo () {
        return $this->hasOne('App\Models\Geo', 'id', 'geo_id')
            ->whereNull('geo.deleted_at')
            ->with('nameTranslation');
    }

    public function edge () {
        return $this->hasOne('App\Models\Edge', 'id', 'edge_id')
            ->whereNull('edge.deleted_at');
    }

    public function roster () {
        return $this->hasOne('App\Models\Roster', 'id', 'roster_id')
            ->whereNull('roster.deleted_at');
    }

    public function photo () {
        return $this->hasOne('App\Models\Photo', 'id', 'photo_id')
            ->whereNull('photo.deleted_at');
    }

    public function respondentGeo () {
        return $this->hasOne('App\Models\RespondentGeo', 'id', 'respondent_geo_id')
            ->with('geo');
    }

    public function respondentName () {
        return $this->hasOne('App\Models\RespondentName', 'id', 'respondent_name_id')
            ->whereNull('respondent_name.deleted_at');
    }
}
