<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EdgeDatum extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'edge_datum';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'edge_id',
        'datum_id'
    ];
}
