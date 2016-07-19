<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DatumRoster extends Model {

    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'datum_roster';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'datum_id',
        'name',
        'read_only'
    ];
}