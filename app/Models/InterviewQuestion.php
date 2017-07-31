<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterviewQuestion extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'interview_question';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'interview_id',
        'question_id',
        'enter_date',
        'answer_date',
        'leave_date',
        'elapsed_time'
    ];
}
