<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CensusType extends Model
{
    public $incrementing = false;

    protected $table = 'census_type';

    protected $fillable = [
        'id',
        'name'
    ];

}
