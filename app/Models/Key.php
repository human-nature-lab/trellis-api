<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Key extends Model
{
    protected $table = 'key';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'key_hash',
        'key_name'
    ];
}
