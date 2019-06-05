<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreloadAction extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'preload_action';

    protected $dates = ['created_at', 'deleted_at'];

    protected $updated_at = '';

    protected $fillable = [
        'id',
        'action_type',
        'payload',
        'respondent_id',
        'question_id',
        'created_at',
        'deleted_at'
    ];

}
