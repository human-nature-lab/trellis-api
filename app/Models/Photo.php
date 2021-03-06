<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Photo extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'photo';

    protected $fillable = [
        'id',
        'file_name',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
