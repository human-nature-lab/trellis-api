<?php

namespace App\Services;

use App\Models\RespondentName;
use Exception;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use App\Models\Respondent;
use App\Models\StudyRespondent;

class RespondentService
{
    /**
     * Create a new respondent and a study_respondent in a transaction
     * @param string $respondentName - The name of the respondent
     * @param string $studyId - The study that the respondent should be assigned to
     * @param string $assignedId - The human readable, assignable id to use for this respondent
     * @return Respondent
     */
    public static function createRespondent ($respondentName, $studyId, $assignedId = "") {

        $newRespondentModel = null;
        DB::transaction(function () use ($respondentName, $studyId, $assignedId, &$newRespondentModel) {
            $respondentId = Uuid::uuid4();

            $newRespondentModel = new Respondent;
            $newRespondentModel->id = $respondentId;
            $newRespondentModel->name = $respondentName;
            $newRespondentModel->assigned_id = $assignedId;
            $newRespondentModel->save();

            self::createRespondentName($respondentId, $respondentName, null, true);

            $studyRespondentId = Uuid::uuid4();
            $newStudyRespondentModel = new StudyRespondent;
            $newStudyRespondentModel->id = $studyRespondentId;
            $newStudyRespondentModel->respondent_id = $respondentId;
            $newStudyRespondentModel->study_id = $studyId;
            $newStudyRespondentModel->save();
        });

        return $newRespondentModel;
    }

    /**
     * Make an edit to an existing respondent name. This method creates a new name and creates a link to the old version
     * of the name.
     * @param {string} $modifiedNameId
     * @param {string} $newName
     * @param {string} $localeId
     * @param {boolean} $isDisplayName
     * @return {RespondentName}
     */
    public static function editRespondentName ($modifiedNameId, $newName = null, $localeId = null, $isDisplayName = false) {
        $oldName = RespondentName::find($modifiedNameId);
        $name = $oldName->replicate();
        $name->fill([
            'id' => Uuid::uuid4(),
            'name' => $newName || $oldName->name,
            'previous_respondent_name_id' => $oldName->id,
            'locale_id' => $localeId || $oldName->locale_id,
            'is_display_name' => $isDisplayName || $oldName->is_display_name
        ]);
        DB::transaction(function () use (&$name, &$oldName) {
            $name->save();
            $oldName->delete();
            if ($name->is_display_name) {
                self::setDisplayNameFalseExceptFor($name->respondent_id, $name->id);
            }
        });
        return $name;
    }

    /**
     * Create a new name for the specified respondent. Using this method also ensures that only one respondent name is
     * the display name at any given time
     * @param string $respondentId - The respondent id
     * @param string $nameString - The actual name string
     * @param string $localeId - The locale to associate this name with
     * @param boolean $isDisplay - If the name is the new display name. True will set all other names to false
     * @return RespondentName
     */
    public static function createRespondentName ($respondentId, $nameString, $localeId, $isDisplay) {
        $name = new RespondentName;
        $name->fill([
            'id' => Uuid::uuid4(),
            'respondent_id' => $respondentId,
            'name' => $nameString,
            'locale_id' => $localeId,
            'is_display_name' => $isDisplay
        ]);
        DB::transaction(function () use ($name) {
            $name->save();
            if ($name->is_display_name) {
                self::setDisplayNameFalseExceptFor($name->respondent_id, $name->id);
            }
        });
        return $name;
    }

    /**
     * Delete a respondent name by id
     * @param {string} $nameId
     * @return RespondentName
     * @throws Exception
     */
    public static function deleteRespondentName ($nameId) {
        $name = RespondentName::find($nameId);
        if ($name->is_display_name) {
            throw new Exception("Can't delete the display name");
        }
        $name->delete();
        return $name;
    }

    /**
     * Make only this respondent name the display name
     * @param $respondentId
     * @param $respondentNameId
     */
    private static function setDisplayNameFalseExceptFor ($respondentId, $respondentNameId) {
        RespondentName::where('respondent_id', $respondentId)
            ->where('id', '!=', $respondentNameId)
            ->update([
                'is_display_name' => false
            ]);
    }
}
