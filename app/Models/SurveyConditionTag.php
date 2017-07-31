<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyConditionTag extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'survey_condition_tag';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'study_id',
        'condition_id'
    ];
}
