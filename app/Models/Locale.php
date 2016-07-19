<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Locale extends Model {

    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'locale';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'language_tag',
        'language_name',
        'language_native'
    ];
}
