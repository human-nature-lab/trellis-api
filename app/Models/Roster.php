<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Roster extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'val',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $table = 'roster';

}
