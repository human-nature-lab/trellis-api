<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model {

    public $incrementing = false;

    protected $table = 'permission';

    public $fillable = [
      'id',
      'type'
    ];

}
