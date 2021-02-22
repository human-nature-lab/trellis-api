<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormType extends Model
{
    // id: 0 = Respondent Form, 1 = Census Form, 2 = Location Form
    public $incrementing = true;

    protected $table = 'form_type';

    protected $fillable = [
        'id',
        'name'
    ];

}
