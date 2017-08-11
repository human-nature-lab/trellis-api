<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserStudy extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'user_study';

    protected $fillable = [
        'id',
        'user_id',
        'study_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
