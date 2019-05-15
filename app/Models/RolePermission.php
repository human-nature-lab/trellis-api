<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RolePermission extends Model {
    use SoftDeletes;

    protected $table = 'role_permission';

    public $fillable = [
      'id',
      'role_id',
      'permission_id',
      'value',
      'created_at',
      'updated_at',
      'deleted_at'
    ];

    public $dates = [
      'created_at',
      'updated_at',
      'deleted_at'
    ];

    public function permission () {
      return $this->belongsTo('App\Models\Permission');
    }

}
