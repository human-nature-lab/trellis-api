<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeoPhoto extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'geo_photo';

    protected $fillable = [
        'id',
        'geo_id',
        'photo_id',
        'sort_order',
        'notes',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function photo () {
        return $this->belongsTo('App\Models\Photo');
    }
}
