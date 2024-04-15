<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model {
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'asset';

    protected $fillable = [
        'id',
        'file_name',
        'type',
        'size',
        'should_sync',
        'mime_type',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public $dates = [
      'created_at',
      'updated_at',
      'deleted_at'
  ];
}
