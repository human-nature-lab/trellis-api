<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupTag extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'group_tag';

    protected $fillable = [
        'id',
        'group_tag_type_id',
        'name',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
