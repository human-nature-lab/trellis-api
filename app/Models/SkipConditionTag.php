<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkipConditionTag extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'skip_condition_tag';

    protected $fillable = [
        'id',
        'skip_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'condition_tag_name'
    ];
}
