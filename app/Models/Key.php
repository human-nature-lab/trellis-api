<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Key extends Model
{
    protected $table = 'key';

    protected $fillable = [
        'id',
        'name',
        'hash',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
