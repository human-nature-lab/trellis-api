<?php

namespace App\Services;

use Ramsey\Uuid\Uuid;
use App\Models\Photo;
use App\Models\RespondentPhoto;

class RespondentPhotoService
{
    public static function createRespondentPhoto ($fileName, $respondentId)
    {
        $photo = new Photo;
        $photoId = Uuid::uuid4();
        $photo->id = $photoId;
        $photo->file_name = $fileName;
        $photo->save();

        $respondentPhoto = new RespondentPhoto;
        $respondentPhoto->id = Uuid::uuid4();
        $respondentPhoto->respondent_id = $respondentId;
        $respondentPhoto->photo_id = $photoId;
        $maxCount = RespondentPhoto::where('respondent_id', $respondentId)->max('sort_order');
        $maxCount = ($maxCount == null) ? 1 : $maxCount + 1;
        $respondentPhoto->sort_order = $maxCount;
        $respondentPhoto->save();

        return $photo;
    }
}
