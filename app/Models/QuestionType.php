<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionType extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'question_type';

    protected $fillable = [
        'id',
        'name',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
