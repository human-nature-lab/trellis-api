<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SelfAdministeredSurvey extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'self_administered_survey';

    protected $fillable = [
        'id',
        'survey_id',
        'login_type',
        'url',
        'password',
        'hash',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
