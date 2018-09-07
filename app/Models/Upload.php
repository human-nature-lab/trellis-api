<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Upload extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'upload';

    protected $fillable = [
        'id',
        'device_id',
        'file_name',
        'hash',
        'status',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
