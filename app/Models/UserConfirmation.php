<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserConfirmation extends Model  {
  use SoftDeletes;

  public $incrementing = false;

  protected $table = 'user_confirmation';

  protected $primaryKey = 'key';

  protected $fillable = [
    'email',
    'name',
    'password',
    'key',
    'is_confirmed',
    'username',
    'created_at',
    'updated_at',
    'deleted_at'
  ];

  protected $hidden = [
    'email',
    'username',
    'password',
    'name',
    'key'
  ];

}
