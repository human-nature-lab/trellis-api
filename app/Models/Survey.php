<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Survey extends Model {

    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'survey';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'respondent_id',
        'form_id',
        'study_id',
        'last_question_id'
    ];
}