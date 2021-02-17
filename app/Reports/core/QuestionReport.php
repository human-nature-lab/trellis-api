<?php

namespace App\Reports\core;

use App\Reports\BaseReport;
use App\Models\Question;
use App\Models\QuestionDatum;
use App\Models\FormSection;

class QuestionReport extends BaseReport {

  public $name = 'question';
  public $configSchema = [
    'questionId' => 'string'
  ];

  public function handle($config) {
    $questionId = $config['questionId'];
    $question = $this->Model('Question')->find($questionId);
    $section = $this->Model('FormSection')->whereIn('section_id', function ($q) use ($questionId) {
      $q->select('section_id')->from('section_question_group')->whereIn('question_group_id', function ($q) use ($questionId) {
        $q->select('question_group_id')->from('question')->where('id', $questionId);
      });
    })->first();
    $headers = $this->makeQuestionHeaders($question, $section);
    $query = $this->Model('QuestionDatum')->where('question_id', $questionId)->with('data');
    $this->mapQuery(function (QuestionDatum $qd) use ($question) {
      $row = $qd->toArray();
      switch ($question->questionType->name) {
        case 'multiple_choice':
        case 'multiple_select': {
            foreach ($qd->data as $datum) {
              $row[$datum->choice_id] = $datum->name;
            }
          }
        default: {
            $vals = $qd->data->map(function ($d) {
              return (string)$d->val;
            })->toArray();
            $row['value'] = implode(';', $vals);
          }
      }
      return $row;
    }, $query, $headers);
  }

  public function makeQuestionHeaders(Question $question, FormSection $section): array {
    $defaultHeaders = [
      'id' => 'id',
      'question_id' => 'question_id',
      'survey_id' => 'survey_id',
      'section_repetition' => 'section_repetition',
      'follow_up_datum_id' => 'follow_up_datum_id',
      'dk_rf' => 'dk_rf',
      'dk_rf_val' => 'dk_rf_val',
      'no_one' => 'no_one',
      'created_at' => 'created_at',
      'updated_at' => 'updated_at',
      'completed_at' => 'completed_at'
    ];

    $headers = [];

    $q = $this->Model('Datum')->whereIn('question_datum_id', function ($sq) use ($question) {
      $sq->select('id')
        ->from('question_datum')
        ->where('question_datum.question_id', '=', $question->follow_up_question_id);
    })
      ->select('sort_order')
      ->distinct();

    switch ($question->questionType->name) {
      case 'multiple_choice':
      case 'multiple_select': {
          foreach ($question->choices as $choice) {
            $headers[$choice->id] = $choice->val;
          }
        }
      default: {
          $headers['value'] = 'value';
        }
    }

    // Sort non default columns first then add default columns
    asort($headers);
    $headers = $defaultHeaders + $headers;
    return $headers;
  }
}
