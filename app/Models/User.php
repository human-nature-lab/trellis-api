<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'user';

    protected $fillable = [
        'id',
        'name',
        'username',
        'password',
        'role',
        'role_id',
        'selected_study_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $hidden = [
        'password',
        'updated_at'
    ];

    public function studies () {
        return $this
            ->belongsToMany('App\Models\Study', 'user_study')
            ->whereNull('user_study.deleted_at')
            ->withTimestamps()
            ->with('locales', 'defaultLocale');
    }

}
