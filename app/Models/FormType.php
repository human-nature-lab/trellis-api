<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormType extends Model
{

    public $incrementing = true;

    protected $table = 'form_type';

    protected $fillable = [
        'id',
        'name'
    ];

}
