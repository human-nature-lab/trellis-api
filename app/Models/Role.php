<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model {
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'role';

    public $fillable = [
      'id',
      'name',
      'can_delete',
      'can_edit',
      'created_at',
      'updated_at',
      'deleted_at'
    ];

    public $dates = [
      'created_at',
      'updated_at',
      'deleted_at'
    ];

    public function permissions () {
      return $this->hasMany('App\Models\RolePermission','role_id', 'id');
    }

}
