<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionAssignConditionTag extends Model {

    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'question_assign_condition_tag';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'question_id',
        'assign_condition_tag_id'
    ];
}