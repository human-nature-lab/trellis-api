<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Datum extends Model {

    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'datum';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'name',
        'val',
        'survey_id',
        'question_id',
        'repetition',
        'parent_datum_id',
        'datum_type_id'
    ];
}