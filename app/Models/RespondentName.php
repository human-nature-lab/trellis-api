<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RespondentName extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'respondent_name';

    protected $fillable = [
        'id',
        'is_display_name',
        'name',
        'respondent_id',
        'previous_respondent_name_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function respondent () {
        return $this->belongsTo('App\Models\Respondent');
    }

}
