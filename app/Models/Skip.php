<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Skip extends Model {

    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'skip';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'show_hide',
        'any_all',
        'precedence'
    ];
}