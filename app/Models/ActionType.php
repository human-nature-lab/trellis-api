<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActionType extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'action';

    protected $fillable = [
        'id',
        'name'
    ];

}
