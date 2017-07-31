<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DatumType extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'datum_type';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'name'
    ];
}
