<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hook extends Model {
  public $incrementing = true;
  public $timestamps = false;

  protected $table = 'hook';

  protected $dates = ['started_at', 'finished_at'];

  protected $fillable = [
    'hook_id',
    'entity_id',
    'instance_id',
    'result',
    'started_at',
    'finished_at',
  ];
}
