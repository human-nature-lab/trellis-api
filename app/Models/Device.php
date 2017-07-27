<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'device';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'device_id',
        'name',
        'epoch'
    ];
}
