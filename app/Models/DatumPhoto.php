<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DatumPhoto extends Model {

    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'datum_photo';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'datum_id',
        'photo_id',
        'sort_order',
        'notes'
    ];
}