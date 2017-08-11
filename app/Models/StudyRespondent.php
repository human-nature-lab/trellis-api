<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudyRespondent extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'study_respondent';

    protected $fillable = [
        'id',
        'study_id',
        'respondent_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
