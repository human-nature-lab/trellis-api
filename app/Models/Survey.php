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
}
