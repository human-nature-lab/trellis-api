<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Snapshot extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'snapshot';

    protected $fillable = [
        'id',
        'file_name',
        'hash',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
