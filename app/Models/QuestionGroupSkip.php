<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionGroupSkip extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'question_group_skip';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'question_group_id',
        'skip_id'
    ];
}
