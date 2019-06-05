<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UploadLog extends Model {
    public $incrementing = true;

    protected $table = 'upload_log';

    protected $fillable = [
        'id',
        'upload_id',
        'table_name',
        'operation',
        'row_id',
        'previous_row',
        'updated_row',
        'created_at',
        'updated_at'
    ];
}
