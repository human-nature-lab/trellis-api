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
        'id',
        'name',
        'val',
        'choice_id',
        'survey_id',
        'datum_type_id',
        'sort_order',
        'created_at',
        'updated_at',
        'deleted_at',
        'question_datum_id',
        'geo_id',
        'edge_id',
        'photo_id',
        'roster_id',
        'event_order'
    ];

    public $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function choice () {
        return $this->hasOne('App\Models\Choice', 'choice')
            ->whereNull('choice.deleted_at')
            ->with('choiceTranslation');
    }

    public function geo () {
        return $this->hasOne('App\Models\Geo', 'geo')
            ->whereNull('geo.deleted_at')
            ->with('nameTranslation');
    }

    public function edge () {
        return $this->hasOne('App\Models\Edge', 'edge')
            ->whereNull('edge.deleted_at');
    }

    public function roster () {
        return $this->hasOne('App\Models\Roster', 'roster')
            ->whereNull('roster.deleted_at');
    }

    public function photo () {
        return $this->hasOne('App\Models\Photo', 'photo')
            ->whereNull('photo.deleted_at');
    }
}
