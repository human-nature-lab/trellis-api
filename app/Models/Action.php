<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Action extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'action';

    protected $fillable = [
        'id',
        'question_id',
        'question_datum_id',
        'survey_id',
        'action_type_id',
        'action_text',
        'created_at',
        'deleted_at'
    ];

    public function type(){
        return $this->hasOne('action_type');
    }

}
