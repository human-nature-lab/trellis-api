<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeoType extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    public function setParentIdAttribute($parent_id)
    {
        $this->attributes['parent_id'] = trim($parent_id) == '' ? null : $parent_id;
    }

    protected $table = 'geo_type';

    protected $fillable = [
        'id',
        'parent_id',
        'study_id',
        'name',
        'can_user_add',
		'can_user_add_child',
        'can_contain_respondent',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
