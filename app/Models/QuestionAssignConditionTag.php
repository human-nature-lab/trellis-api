<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionAssignConditionTag extends Pivot
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'question_assign_condition_tag';

    protected $fillable = [
        'id',
        'question_id',
        'assign_condition_tag_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
