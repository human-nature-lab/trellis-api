<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionParameter extends Model {

    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'question_parameter';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'question_id',
        'parameter_id',
        'val'
    ];
}