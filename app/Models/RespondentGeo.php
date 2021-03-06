<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class RespondentGeo extends Pivot
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'respondent_geo';

    protected $fillable = [
        'id',
        'geo_id',
        'respondent_id',
        'previous_respondent_geo_id',
        'notes',
        'is_current',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function geo () {
        return $this->belongsTo('App\Models\Geo')->with('geoType', 'nameTranslation');
    }

}
