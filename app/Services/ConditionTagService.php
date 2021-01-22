<?php

namespace app\Services;

use App\Library\CsvFileReader;
use App\Models\ConditionTag;
use App\Models\RespondentConditionTag;
use App\Services\RespondentService;
use Ramsey\Uuid\Uuid;
use DB;
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
      $respondentConditionTag->save();
      return $respondentConditionTag;
    }

    public function importRespondentConditionTagsFromFile (String $filePath, String $studyId): array {

      $row = 0;
      $importedRCTIds = [];
      try {
        $csv = new CsvFileReader($filePath);
        $csv->open();
        $line = $csv->getNextRowHash();
        while ($line) {
          $respondentAssignedId = trim($line['respondent_id']);
          $conditionTagName = trim($line['condition_tag']);
          $conditionTag = ConditionTagService::createConditionTag($conditionTagName);
          $respondent = RespondentService::lookupRespondentById($respondentAssignedId, $studyId);
          if (!isset($respondent)) {
            throw new Exception("Unable to find respondent with id matching $respondentAssignedId");
          }
          $rct = $this->createRespondentConditionTag($respondent->id, $conditionTag);
          array_push($importedRCTIds, $rct->id);
          $row++;
          $line = $csv->getNextRowHash();
        }
      } finally {
        $csv->close();
      }

      return $importedRCTIds;

    }
}
