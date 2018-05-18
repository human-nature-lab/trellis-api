<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Edge extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'edge';

    protected $fillable = [
        'id',
        'source_respondent_id',
        'target_respondent_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];


    public function targetRespondent () {
        return $this->hasOne('App\Models\Respondent', 'id', 'target_respondent_id')->with('photos');
    }

    public function sourceRespondent () {
        return $this->hasOne('App\Models\Respondent', 'id', 'source_respondent_id')->with('photos');
    }
}
