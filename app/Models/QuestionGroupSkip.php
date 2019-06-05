<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionGroupSkip extends Pivot
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'question_group_skip';

    protected $fillable = [
        'id',
        'question_group_id',
        'skip_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
