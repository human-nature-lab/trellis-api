<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Action extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'action';

    protected $fillable = [
        'id',
        'survey_id',
        'question_id',
        'created_at',
        'deleted_at',
        'action_type',
        'payload',
        'section',
        'page',
        'section_follow_up_repetition',
        'section_repetition'
    ];

}
