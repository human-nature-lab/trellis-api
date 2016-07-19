<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Log extends Model {

    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'log';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'actor_id',
        'row_id',
        'table_name',
        'operation',
        'previous_row'
    ];
}