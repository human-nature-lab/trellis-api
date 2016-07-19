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
        'created_at',
        'updated_at',
        'deleted_at',
        'geo_id',
        'name'
    ];
}