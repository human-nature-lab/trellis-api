<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RespondentFill extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'respondent_fill';

    protected $fillable = [
        'id',
		'respondent_id',
		'name',
		'val',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
