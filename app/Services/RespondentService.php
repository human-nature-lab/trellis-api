<?php

namespace App\Services;

use App\Models\RespondentGeo;
use App\Models\RespondentName;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
     * @param {string} [$geoId] - A single geo id to add this respondent to
     * @param {string} [$associatedRespondentId] - A respondent to associate this respondent with
     * @return Respondent
     */
    public static function createRespondent ($respondentName, $studyId, $assignedId = null, $geoId = null, $associatedRespondentId = null) {

        $newRespondentModel = null;
        DB::transaction(function () use ($respondentName, $studyId, $assignedId, &$newRespondentModel, $geoId, $associatedRespondentId) {
            $respondentId = Uuid::uuid4();

            $newRespondentModel = new Respondent;
            $newRespondentModel->id = $respondentId;
            $newRespondentModel->name = $respondentName;
            $newRespondentModel->assigned_id = $assignedId;
            $newRespondentModel->geo_id = $geoId;
            $newRespondentModel->associated_respondent_id = $associatedRespondentId;
            $newRespondentModel->save();

            // Create the corresponding respondent geo
            if ($geoId) {
                self::createRespondentGeo($respondentId, $geoId, true);
            }

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
     * Create a respondent geo for the given respondent
     * @param {string} $respondentId
     * @param {string} $geoId
     * @param {bool} $isCurrent
     * @param {string} $notes
     * @param {string} $previousRespondentGeoId
     * @returns RespondentGeo
     */
    public static function createRespondentGeo ($respondentId, $geoId, $isCurrent = false, $notes = null, $previousRespondentGeoId= null) {
        $rGeo = new RespondentGeo;
        $rGeo->fill([
            'id' => Uuid::uuid4(),
            'respondent_id' => $respondentId,
            'geo_id' => $geoId,
            'is_current' => $isCurrent,
            'notes' => $notes,
            'previous_respondent_geo_id' => $previousRespondentGeoId
        ]);
        DB::transaction(function () use ($rGeo) {
            $rGeo->save();
            if ($rGeo->is_current) {
                self::setIsCurrentFalseExceptFor($rGeo->respondent_id, $rGeo->id);
            }
        });
        return $rGeo;
    }

    /**
     * Delete the old respondent_geo and link it to the new respondent geo with the new geo_id
     * @param {string} $oldRespondentGeoId
     * @param {string} $newGeoId
     * @return RespondentGeo
     */
    public static function moveRespondentGeo ($oldRespondentGeoId, $newGeoId) {
        $oldRGeo = RespondentGeo::find($oldRespondentGeoId);
        $newRGeo = $oldRGeo->replicate();
        $newRGeo->fill([
            'id' => Uuid::uuid4(),
            'geo_id' => $newGeoId,
            'previous_respondent_geo_id' => $oldRGeo->id
        ]);

        DB::transaction(function () use (&$newRGeo, &$oldRGeo) {
            $newRGeo->save();
            $oldRGeo->delete();
            if ($oldRGeo->is_current) {
                self::setIsCurrentFalseExceptFor($newRGeo->respondent_id, $newRGeo->id);
            }
        });

        return $newRGeo;
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
            'name' => $newName,
            'previous_respondent_name_id' => $oldName->id,
            'locale_id' => $localeId,
            'is_display_name' => $isDisplayName === true
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
        $rName = new RespondentName;
        $rName->fill([
            'id' => Uuid::uuid4(),
            'respondent_id' => $respondentId,
            'name' => $nameString,
            'locale_id' => $localeId,
            'is_display_name' => $isDisplay
        ]);
        DB::transaction(function () use ($rName) {
            $rName->save();
            if ($rName->is_display_name) {
                self::setDisplayNameFalseExceptFor($rName->respondent_id, $rName->id);
            }
        });
        return $rName;
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
     * @param {string} $respondentId
     * @param {string} $respondentNameId
     */
    private static function setDisplayNameFalseExceptFor ($respondentId, $respondentNameId) {
        RespondentName::where('respondent_id', $respondentId)
            ->where('id', '!=', $respondentNameId)
            ->update([
                'is_display_name' => false
            ]);
    }

    /**
     * Make only this respondent geo the current respondent geo
     * @param {string} $respondentId
     * @param {string} $respondentGeoId
     */
    private static function setIsCurrentFalseExceptFor ($respondentId, $respondentGeoId) {
        RespondentGeo::where('respondent_id', $respondentId)
            ->where('id', '!=', $respondentGeoId)
            ->update([
                'is_current' => false
            ]);
    }
}
