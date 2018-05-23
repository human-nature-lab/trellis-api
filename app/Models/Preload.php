<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Preload extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'preload';

    protected $fillable = [
        'id',
        'respondent_id',
        'form_id',
        'study_id',
        'last_question_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'completed_at'
    ];

    public function data () {
        return $this->hasMany('App\Models\Datum', 'preload_id', 'id');
    }

}
