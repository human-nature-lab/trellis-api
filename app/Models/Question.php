<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model {

    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'question';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'question_type_id',
        'question_translation_id',
        'question_group_id',
        'sort_order',
        'var_name'
    ];
}