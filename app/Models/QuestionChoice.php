<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

use DB;

class QuestionChoice extends Pivot
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'question_choice';

    protected $fillable = [
        'id',
        'question_id',
        'choice_id',
        'sort_order',
        'created_at',
        'updated_at',
        'deleted_at'
    ];


    public function choice () {
        return $this->hasOne('App\Models\Choice', 'id', 'choice_id')
            ->with('choiceTranslation');
    }

    /*
    public function delete()
    {

        // Delete orphaned choices
        Choice::where('id', $this->choice_id)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                   ->from('question_choice')
                   ->whereNull('deleted_at')
                   ->where('id', '<>', $this->id)
                   ->where('choice_id', $this->choice_id);
            })
            ->delete();

        return parent::delete();
    }
    */
}
