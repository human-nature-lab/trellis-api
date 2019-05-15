<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FailedJob extends Model
{
    use SoftDeletes;

    protected $table = 'failed_jobs';

    protected $fillable = [
		'id',
		'connection',
        'exception',
		'queue',
		'payload',
		'failed_at'
    ];
}
