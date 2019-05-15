<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'device';

    protected $fillable = [
        'id',
        'device_id',
        'name',
        'key',
        'added_by_user_id',
        'epoch',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $hidden = [
        'key'
    ];

    public function addedByUser () {
        return $this->hasOne('App\Models\User', 'id', 'added_by_user_id');
    }
}
