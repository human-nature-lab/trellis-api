<?php

namespace app\Services;

use App\Library\CsvFileReader;
use App\Models\ConditionTag;
use App\Models\Respondent;
use App\Models\RespondentConditionTag;
use Ramsey\Uuid\Uuid;
use DB;
use Log;
use \Exception;

class ConditionTagService {

    public static function createConditionTag (String $name): ConditionTag {
        $newConditionTag = new ConditionTag;
        $conditionTagId = Uuid::uuid4();
        DB::transaction(function () use ($newConditionTag, $conditionTagId, $name) {
            $newConditionTag->id = $conditionTagId;
            $newConditionTag->name = $name;
            $newConditionTag->save();
        });
        return $newConditionTag;
    }

    public function createRespondentConditionTag (String $respondentId, ConditionTag $conditionTag): RespondentConditionTag {
      $respondentConditionTag = new RespondentConditionTag();
      $respondentConditionTag->id = Uuid::uuid4();
      $respondentConditionTag->condition_tag_id = $conditionTag->id;
      $respondentConditionTag->respondent_id = $respondentId;
      return $respondentConditionTag;
    }

    public function importRespondentConditionTagsFromFile (String $filePath, String $studyId): array {

      $row = 0;
      $importedRCTIds = [];
      try {
        $csv = new CsvFileReader($filePath);
        $csv->open();
        $line = $csv->getNextRowHash();
        while (isset($line)) {
          Log::info(json_encode($line));
          $line = $csv->getNextRowHash();
          $respondentAssignedId = trim($line['respondent_id']);
          $conditionTagName = trim($line['condition_tag']);
          $conditionTag = ConditionTagService::createConditionTag($conditionTagName);
          $respondent = Respondent::whereIn('id', function ($q) use ($studyId) {
            return $q->select('respondent_id')->from('study_respondent')->where('study_id', $studyId);
          })->where(function ($q) use ($respondentAssignedId) {
            return $q->where('id', $respondentAssignedId)->orWhere('assigned_id', $respondentAssignedId);
          })->first();
          if (!isset($respondent)) {
            throw new Exception("Unable to find respondent with id matching $respondentAssignedId");
          }
          $rct = $this->createRespondentConditionTag($respondent->id, $conditionTag);
          array_push($importedRCTIds, $rct->id);
          $row++;
        }
      } finally {
        $csv->close();
      }

      return $importedRCTIds;

    }
}
