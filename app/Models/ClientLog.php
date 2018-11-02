<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientLog extends Model
{
    public $incrementing = false;

    protected $table = 'client_log';

    protected $fillable = [
        'id',
        'message',
        'full_message',
        'severity',
        'component',
        'sync_id',
        'interview_id',
        'device_id',
        'user_id',
        'version',
        'offline',
        'user_agent',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
