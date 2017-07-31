<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Edge extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'edge';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'source_respondent_id',
        'target_respondent_id'
    ];
}
