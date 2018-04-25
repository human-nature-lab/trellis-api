<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Datum extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'datum';

    protected $fillable = [
        'id',
        'name',
        'val',
        'choice_id',
        'survey_id',
        'datum_type_id',
        'sort_order',
        'question_datum_id',
        'geo_id',
        'edge_id',
        'photo_id',
        'roster_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function choices () {
        return $this->hasMany('App\Models\Choice', 'choice')
            ->whereNull('choice.deleted_at')
            ->with('choiceTranslation');
    }

    public function geos () {
        return $this->hasMany('App\Models\Geo', 'geo')
            ->whereNull('geo.deleted_at');
    }

    public function edges () {
        return $this->hasMany('App\Models\Edge', 'edge')
            ->whereNull('edge.deleted_at');
    }

    public function rosters () {
        return $this->hasMany('App\Models\Roster', 'roster')
            ->whereNull('roster.deleted_at');
    }
}
