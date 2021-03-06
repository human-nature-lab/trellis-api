<?php
/**
 * Created by IntelliJ IDEA.
 * User: wi27
 * Date: 4/18/2018
 * Time: 1:14 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionDatum extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'question_datum';

    protected $fillable = [
        'id',
        'section_repetition',
        'follow_up_datum_id',
        'question_id',
        'survey_id',
        'preload_id',
        'answered_at',
        'skipped_at',
        'created_at',
        'updated_at',
        'deleted_at',
        'dk_rf',
        'dk_rf_val'
    ];

    public $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function data () {
        return $this->hasMany('App\Models\Datum', 'question_datum_id')
            ->whereNull('datum.deleted_at');
    }

    public function fullData () {
        return $this->hasMany('App\Models\Datum', 'question_datum_id')
            ->whereNull('datum.deleted_at')
            ->with('roster', 'choice', 'edge', 'photo', 'geo', 'respondentGeo', 'respondentName');
    }

}