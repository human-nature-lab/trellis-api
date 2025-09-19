<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class SectionQuestionGroup extends Pivot
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'section_question_group';

    protected $fillable = [
        'id',
        'section_id',
        'question_group_id',
        'question_group_order',
        'randomize_questions',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

}
