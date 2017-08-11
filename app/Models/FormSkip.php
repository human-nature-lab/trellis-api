<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormSkip extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'form_skip';

    protected $fillable = [
        'id',
        'form_id',
        'skip_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
