<?php

namespace App\Services;

use App\Models\PreloadAction;
use App\Models\Roster;
use App\Models\Question;
use App\Models\Edge;
use App\Models\Choice;
use App\Library\CsvFileReader;
use App\Models\Geo;
use App\Models\GeoType;
use App\Models\Photo;
use App\Models\Translation;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PreloadActionService {

  private static $validActions = [
    'select-choice' => ['multiple_choice', 'multiple_select'],
    'other-choice-text' => ['multiple_choice', 'multiple_select'],
    // 'dk-rf' => ['*'],
    // 'dk-rf-val' => ['*'],
    'number-change' => ['decimal', 'integer'],
    'add-edge' => ['relationship'],
    'no-one' => ['relationship'],
    'add-roster-row' => 'roster',
    // 'change-sort-order',
    'set-val' => ['text', 'text_area'],
    'add-geo' => ['geo'],
    'add-photo' => ['image'],
    'set-date' => ['year', 'year_month', 'year_month_day', 'year_month_day_time'],
    'set-text' => ['text', 'text_area'],
    'set-time' => ['time', 'year_month_day_time'],
    // 'respondent-move',
    // 'respondent-add-geo',
    // 'other-respondent-added',
    // 'select-no-one',
  ];

  /**
   * Throws an exception if any of the keys aren't present in the array
   */
  private static function mustHaveKeys (Array $arr, Array $keys) {
    foreach ($keys as $key) {
      if (!isset($arr[$key])) {
        throw new \Exception("Array doesn't contain key: $key");
      }
    }
  }

  private static function insertPreloadRow (string $studyId, Question $question, Array $row) {
    $payload = null;
    $type = $row['action_type'];
    $line = $row['line'];
    switch ($type) {
      case 'add-edge':
        PreloadActionService::mustHaveKeys($row, ['edge.target_respondent_id', 'edge.source_respondent_id']);
        $edge = Edge::create([
          'id' => Uuid::uuid4(),
          'target_respondent_id' => $row['edge.target_respondent_id'],
          'source_respondent_id' => $row['edge.source_respondent_id'],
        ]);
        $payload = [
          'edge_id' => $edge->id,
          'val' => $edge->id,
        ];
        break;
      case 'select-choice':
        PreloadActionService::mustHaveKeys($row, ['choice.val']);
        $val = $row['choice.val'];
        $choice = Choice::whereIn('id', function ($q) use ($question) {
          $q->select('choice_id')->from('question_choice')->where('question_id', $question->id);
        })->where('val', $val)->first();
        if (!isset($choice)) {
          throw new \Exception("invalid choice of $val");
        }
        $payload = [
          'choice_id' => $choice->id,
          'val' => $choice->val,
        ];
        break;
      case 'set-text':
      case 'set-val':
      case 'set-date':
      case 'set-time':
      case 'number-change':
        PreloadActionService::mustHaveKeys($row, ['payload.val']);
        $payload = ['val' => $row['payload.val']];
        break;
      case 'add-geo':
        $geo = PreloadActionService::addGeo($row);
        $payload = ['geo_id' => $geo->id];
        break;
      case 'add-roster-row':
        PreloadActionService::mustHaveKeys($row, ['roster.val']);
        $roster = Roster::create([
          'id' => Uuid::uuid4(),
          'val' => $row['roster.val'],
        ]);
        $payload = ['roster_id' => $roster->id];
        break;
      case 'add-photo':
        PreloadActionService::mustHaveKeys($row, ['photo.id']);
        $photo = Photo::find($row['photo.id']);
        if (!isset($photo)) {
          throw new \Exception("Invalid photo.id at line: $line");
        }
        $payload = ['photo_id' => $photo->id];
        break;
      case 'no-one':
        $payload = [];
        break;
    }
    if ($payload === null) {
      $line = $row['line'];
      throw new \Exception("no handler for action_type ($type) at line: $line");
    }
    return PreloadAction::create([
      'id' => Uuid::uuid4(),
      'action_type' => $row['action_type'],
      'respondent_id' => $row['respondent_id'],
      'question_id' => $question->id,
      'payload' => json_encode($payload),
    ]);
  }

  private static function addGeo (Array $row): Geo {
    if (isset($row['location.id']) && $row['location.id'] !== null) {
      $geo = Geo::find($row['location.id']);
      if (!isset($geo)) {
        $line = $row['line'];
        throw new \Exception("geo.id is invalid at line: $line");
      }
      return $geo;
    }
    $geoType = PreloadActionService::getGeoType($row);
    $nameTranslation = TranslationService::createTranslationFromArray($row, 'location.');
    $data = [
      'id' => Uuid::uuid4(),
      'geo_type_id' => $geoType->id,
      'name_translation_id' => $nameTranslation->id,
      'latitude' => $row['location.latitude'],
      'longitude' => $row['location.longitude'],
      'altitude' => $row['location.altitude'],
    ];
    if (array_key_exists('location.parent_id', $row)) {
      $data['parent_id'] = $row['location.parent_id'];
    }
    if (array_key_exists('location.altitude', $row)) {
      $data['altitude'] = $row['location.altitude'];
    }
    return Geo::create($data);
  }

  private static function getGeoType (Array $row): GeoType {
    $line = $row['line'];
    if (isset($row['location.type.id']) && $row['location.type.id'] !== null) {
      $geo = GeoType::find($row['location.type.id']);
      if (!isset($geo)) {
        $gId = $row['location.type.id'];
        throw new \Exception("geo.type.id ($gId) does not exist at line: $line");
      }
      return $geo;
    } else if (isset($row['location.type.name']) && $row['location.type.name'] !== null) {
      $name = $row['location.type.name'];
      $geo = GeoType::where('name', 'like', $name)->get();
      if (count($geo) > 1) {
        throw new \Exception("geo.type.name ($name) must be unique across all studies at line: $line");
      } else if (count($geo) === 0) {
        throw new \Exception("geo.type.name ($name) does not exist at line: $line");
      } else {
        return $geo[0];
      }
    } else {
      throw new \Exception("Must define geo.type.id or geo.type.name at line: $line");
    }
  }

  /**
   * Fetches a question using either the question.id or question.var_name headers
   */
  private static function getQuestion (Array $row): Question {
    $line = $row['line'];
    if (isset($row['question.id']) && $row['question.id'] !== null) {
      $question = Question::find($row['question.id']);
      if (!isset($question)) {
        $qId = $row['question.id'];
        throw new \Exception("question.id ($qId) does not exist at line: $line");
      }
      return $question;
    } else if (isset($row['question.var_name']) && $row['question.var_name'] !== null) {
      $varName = $row['question.var_name'];
      $questions = Question::where('var_name', $varName)->get();
      if (count($questions) > 1) {
        throw new \Exception("question.var_name ($varName) must be unique across all forms and studies at line: $line");
      } else if (count($questions) === 0) {
        throw new \Exception("question.var_name ($varName) does not exist at line: $line");
      } else {
        return $questions[0];
      }
    } else {
      throw new \Exception("Must define question.id or question.var_name at line: $line");
    }
  }

  public static function importPreloadData (string $studyId, string $filePath): Array {
    $csv = new CsvFileReader($filePath, false);
    $actions = [];
    try {
      $csv->open();

      // All CSVs must have these headers at minimum
      $minimalHeaders = ['respondent_id', 'action_type'];
      if (!$csv->hasHeaders($minimalHeaders)) {
        $headers = implode(',', $minimalHeaders);
        throw new \Exception("invalid CSV schema. must have at least: $headers");
      }

      DB::beginTransaction();

      $line = 1;
      $row = $csv->getNextRowHash();
      while ($row !== false) {
        $row['line'] = $line;
        $type = $row['action_type'];
        if (!isset(PreloadActionService::$validActions[$type])) {
          throw new \Exception("invalid action_type of $type at line: $line");
        }

        // validate the question and respondent
        $validator = Validator::make([
          'respondent_id' => $row['respondent_id'],
        ], [
          'respondent_id' => 'required|exists:respondent,id',
        ]);
        if ($validator->fails() ) {
          throw new \Exception($validator->errors() . " at line: $line");
        }

        // Validate the actionType works for this question
        $question = PreloadActionService::getQuestion($row);
        if (!isset($question)) {
          throw new \Exception("Invalid question id at line: $line");
        }
        if (!in_array($question->questionType->name, PreloadActionService::$validActions[$type])) {
          $qt = $question->questionType->name;
          throw new \Exception("Invalid action type ($type) for question type of $qt");
        }
        
        // Actually insert each question type
        $actions[] = PreloadActionService::insertPreloadRow($studyId, $question, $row);
        $line++;
        $row = $csv->getNextRowHash();
      }
      // DB::commit();
      DB::rollBack();
      return $actions;
    } catch (\Exception $err) {
      DB::rollBack();
      throw $err;
    } finally {
      $csv->close();
    }
  }

  public static function preloadAddRosterRow($respondentId, $questionId, $payload) {
    // Create roster row
    $rosterId = Uuid::uuid4();
    $rosterModel = new Roster;
    $rosterModel->id = $rosterId;
    $rosterModel->val = $payload;
    $rosterModel->save();

    // Create preload action
    $preloadActionId = Uuid::uuid4();
    $preloadActionModel = new PreloadAction;
    $preloadActionModel->id = $preloadActionId;
    $preloadActionModel->action_type = 'add-roster-row';
    $preloadActionModel->respondent_id = $respondentId;
    $preloadActionModel->question_id = $questionId;
    $preloadActionModel->payload = '{"roster_id":"' . $rosterId . '"}';
    $preloadActionModel->save();

    return $preloadActionModel;
  }
}
