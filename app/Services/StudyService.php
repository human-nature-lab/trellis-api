<?php

namespace App\Services;

use App\Models\Study;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class StudyService
{
    public static function getActiveStudiesPaginated($perPage)
    {
        $studies = Study::where('study.deleted_at', null)
            ->orderBy('created_at')
            ->paginate($perPage);

        return $studies;
    }

    public static function getStudies()
    {
        $studies = Study::where('study.deleted_at', null)
            ->orderBy('created_at')
            ->get();

        return $studies;
    }

    public static function getStudyById($studyId)
    {
        $study = Study::where('study.deleted_at', null)
            ->where('id', $studyId)
            ->get();

        return $study;
    }

    public static function getStudy($id)
    {
        $study = Study::find($id);

        return $study;
    }

    public static function getActiveStudy($userId)
    {
        $study = Study::join('user', 'user.selected_study_id', '=', 'study.id')
            ->where('user.id', $userId)
            ->first();

        return $study;
    }

    public static function createNewStudy($request)
    {
        $study = new Study();
        $study->id = Uuid::uuid4();
        $study->name = $request->input('name');
        $study->photo_quality = $request->input('photo_quality');
        $study->default_locale_id = $request->input('locale');

        $study->save();

        return $study;
    }

    public static function updateStudy($request, $id)
    {
        $study = Study::find($id);

        $study->name = $request->input('name');
        $study->photo_quality = $request->input('photo_quality');
        $study->locale = $request->input('locale');
        $study->save();

        return $study;
    }

    public static function deleteStudy($id)
    {
        $study = Study::destroy($id);

        return;
    }

    public static function selectStudy($userId, $studyId)
    {
        $user = User::where('id', $userId)
            ->update(['selected_study_id', $studyId]);
    }

    public static function getLocaleByStudyId($studyId)
    {
        $localeId = Study::where('deleted_at', null)
            ->where('id', $studyId)
            ->value('default_locale_id');

        return $localeId;
    }
}
