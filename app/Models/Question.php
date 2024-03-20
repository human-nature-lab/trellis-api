<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'question';

    protected $fillable = [
        'id',
        'question_type_id',
        'question_translation_id',
        'question_group_id',
        'sort_order',
        'var_name',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function questionTranslation()
    {
        return $this
            ->belongsTo('App\Models\Translation', 'question_translation_id')
            ->with('translationText');
    }

    public function questionType()
    {
        return $this->belongsTo('App\Models\QuestionType', 'question_type_id');
    }

    public function questionParameters()
    {
        return $this
            ->hasMany('App\Models\QuestionParameter', 'question_id')
            ->with('parameter');
    }

    public function choices()
    {
        return $this
            ->belongsToMany('App\Models\Choice', 'question_choice')
            ->using('App\Models\QuestionChoice')
            ->withPivot('sort_order', 'id')
            ->whereNull('question_choice.deleted_at')
            ->withTimestamps()
            ->with('choiceTranslation');
    }

    public function assignConditionTags()
    {
        return $this
            ->belongsToMany('App\Models\AssignConditionTag', 'question_assign_condition_tag')
            ->using('App\Models\QuestionAssignConditionTag')
            ->withPivot('question_id')
            ->whereNull('question_assign_condition_tag.deleted_at')
            ->withTimestamps()
            ->with('condition');
    }

    public function preloadActions () {
      return $this->hasMany('App\Models\PreloadAction');
    }

    public function sliderLabels () {
      $integerTypeId = '2d3ff07a-5ab1-4da0-aa7f-440cf8cd0980';
      $decimalTypeId = '312533dd-5957-453c-ab00-691f869d257f';
      $tickLabelParameterId = '35';
      if ($this->question_type_id === $integerTypeId || $this->question_type_id === $decimalTypeId) {
        $qp = $this->questionParameters->first(function($qp) use ($tickLabelParameterId) {
          return $qp->parameter_id === $tickLabelParameterId;
        });
        if (!$qp) {
          return [];
        }
        $labelIds = collect(json_decode($qp->value))->map(function($label) { return $label->translation_id; });
        return Translation::with('translationText')->whereIn('id', $labelIds);
      }
      return [];
    }

    /*
    public function delete()
    {
        //\Log::info('Question->delete()');
        QuestionChoice::where('question_id', $this->id)
            ->delete();

        QuestionParameter::where('question_id', $this->id)
            ->delete();

        QuestionAssignConditionTag::where('question_id', $this->id)
            ->delete();

        return parent::delete();
    }
    */
}
