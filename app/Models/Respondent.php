<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Respondent extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'respondent';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'geo_id',
        'name'
    ];

    public function photos() {
        return $this
        ->belongsToMany('App\Models\Photo', 'respondent_photo')
        ->whereNull('respondent_photo.deleted_at')
        ->withTimestamps();
    }

    public function studies() {
        return $this->belongsToMany('App\Models\Study', 'study_respondent')
        ->whereNull('study_respondent.deleted_at')
        ->withTimestamps();
    }

    public function delete() {
        StudyRespondent::where('respondent_id', $this->id)
            ->delete();

        RespondentPhoto::where('respondent_id', $this->id)
            ->delete();

        RespondentConditionTag::where('respondent_id', $this->id)
            ->delete();

        RespondentGroupTag::where('respondent_id', $this->id)
            ->delete();

        return parent::delete();
    }
}