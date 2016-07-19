<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudyForm extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'study_form';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'study_id',
        'form_master_id',
        'sort_order'
    ];
}
