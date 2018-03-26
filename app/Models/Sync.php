<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sync extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'sync';

    protected $fillable = [
        'id',
        'device_id',
        'snapshot_id',
        'file_name',
        'type',
        'status',
        'error_message',
        'warning_message',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
