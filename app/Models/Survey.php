<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Survey extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'survey';

    protected $fillable = [
        'id',
        'respondent_id',
        'form_id',
        'study_id',
        'last_question_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'completed_at'
    ];

    /*
    public function delete()
    {
        $childDatum = Datum::where('survey_id', '=', $this->id)->get();
        foreach ($childDatum as $childData) {
            $childData->delete();
        }

        return parent::delete();
    }
    */

    public function interviews(){
        return $this->hasMany("App\Models\Interview", "survey_id")
            ->with("user");
    }

    public function dataCount(){
        return $this->select(function($query){
            $query->from("datum")
                ->where("datum.survey_id", "=", "survey.id");
        });
    }

    public function form(){
        return $this->belongsTo("App\Models\Form", "form_id")
            ->with("nameTranslation");
    }

    public function respondent(){
        return $this->belongsTo("App\Models\Respondent", "respondent_id");
    }


    public function data () {
        return $this->hasMany('App\Models\QuestionDatum', 'survey_id')
            ->whereNull('question_datum.deleted_at')
            ->with('data');
    }

    public function sectionConditionTags () {
        return $this->hasMany('App\Models\SectionConditionTag', 'survey_id')
            ->whereNull('section_condition_tag.deleted_at');
    }

    public function surveyConditionTags () {
        return $this->hasMany('App\Models\SurveyConditionTag', 'survey_id')
            ->whereNull('survey_condition_tag.deleted_at');
    }

    public function respondentConditionTags () {
        return $this->hasMany('App\Models\RespondentConditionTag', 'respondent_id', 'respondent_id')
            ->with('conditionTag');
    }
}
