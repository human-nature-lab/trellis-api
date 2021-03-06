<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormSkip extends Pivot
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
