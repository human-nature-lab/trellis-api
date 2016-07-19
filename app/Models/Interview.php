<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Interview extends Model {

    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'interview';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'survey_id',
        'user_id',
        'start_time',
        'end_time',
        'latitude',
        'longitude',
        'altitude'
    ];
}