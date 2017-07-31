<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhotoTag extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'photo_tag';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'photo_id',
        'tag_id'
    ];
}
