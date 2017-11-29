<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use SoftDeletes;

    protected $table = 'jobs';

    protected $fillable = [
		'id',
		'queue',
		'payload',
		'attempts',
		'reserved',
		'reserved_at',
		'available_at',
		'created_at'
    ];
}
