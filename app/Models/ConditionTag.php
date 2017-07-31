<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConditionTag extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'condition_tag';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'name'
    ];

    public function skips()
    {
        return $this
            ->belongsToMany('App\Models\Skip', 'skip_condition_tag')
            ->whereNull('skip_condition_tag.deleted_at')
            ->withTimestamps();
    }
}
