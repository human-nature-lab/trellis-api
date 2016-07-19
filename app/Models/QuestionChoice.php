<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionChoice extends Model {

    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'question_choice';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'question_id',
        'choice_id',
        'sort_order'
    ];
}