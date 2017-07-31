<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DatumGeo extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'datum_geo';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'datum_id',
        'geo_id',
        'sort_order'
    ];
}
