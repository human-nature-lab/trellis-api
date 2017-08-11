<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DatumGroupTag extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'datum_group_tag';

    protected $fillable = [
        'id',
        'datum_id',
        'group_tag_id',
        'sort_order',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
