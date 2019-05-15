<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Config extends Model {

    public $incrementing = false;

    protected $table = 'config';

    protected $primaryKey = 'key';

    protected $dates = ['updated_at', 'created_at', 'deleted_at'];

    protected $fillable = [
        'key',
        'value',
        'is_public',
        'type',
        'default_value',
        'updated_at',
        'created_at',
        'deleted_at'
    ];
}
