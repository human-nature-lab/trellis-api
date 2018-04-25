<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Roster extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'roster';

    protected $fillable = [
        'id',
        'val',
        'updated_at',
        'created_at',
        'deleted_at'
    ];

}
