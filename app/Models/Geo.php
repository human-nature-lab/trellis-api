<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Geo extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'geo';

    protected $fillable = [
      'id',
      'assigned_id',
      'geo_type_id',
      'parent_id',
      'latitude',
      'longitude',
      'altitude',
      'name_translation_id',
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

    public function geoType()
    {
        return $this
            ->belongsTo('App\Models\GeoType', 'geo_type_id');
    }

    public function photos() {
        return $this
            ->belongsToMany('App\Models\Photo', 'geo_photo', 'geo_id', 'photo_id')
            ->whereNull('geo_photo.deleted_at')
            ->where('geo_photo.sort_order', '=', '0')
            ->withTimestamps();
    }

    public function parent()
    {
        return $this
            ->belongsTo('App\Models\Geo', 'parent_id')
            ->with('nameTranslation', 'geoType', 'parent');
    }
}
