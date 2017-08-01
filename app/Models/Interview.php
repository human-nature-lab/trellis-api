<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Interview extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'interview';

    protected $fillable = [
        'id',
        'survey_id',
        'user_id',
        'start_time',
        'end_time',
        'latitude',
        'longitude',
        'altitude',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
