<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DatumChoice extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'datum_choice';

    protected $fillable = [
        'id',
        'datum_id',
        'choice_id',
        'sort_order',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
