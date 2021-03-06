<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SectionConditionTag extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'section_condition_tag';

    protected $fillable = [
        'id',
        'section_id',
        'condition_id',
        'survey_id',
        'repetition',
        'follow_up_datum_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
