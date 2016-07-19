<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Form extends Model {

    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'form';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'form_master_id',
        'name_translation_id',
        'version'
    ];
}