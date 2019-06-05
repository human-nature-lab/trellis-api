<?php

namespace App\Services;

use App\Library\CsvFileReader;
use App\Models\Geo;
use App\Models\GeoPhoto;
use App\Models\GeoType;
use App\Models\Photo;
use App\Models\Study;
use Ramsey\Uuid\Uuid;
use Exception;
use Throwable;
use ZipArchive;

class GeoService {

  function importGeosFromFile (String $filePath, String $studyId): array {

    $fileReader = new CsvFileReader($filePath);
    $fileReader->open();

    $row = $fileReader->getNextRowHash();

    $study = Study::find($studyId);

    if (!isset($study)) {
      throw new Exception('No study with this idea exists');
    }

    $geoIds = [];

    while ($row) {

      $name = $row['name'];
      $assignedId = $row['id'];
      $geoTypeName = $row['geo_type'];
      $assignedParentId = array_key_exists('parent_id', $row) ? $row['parent_id'] : null;
      $lat = array_key_exists('latitude', $row) ? $row['latitude'] : null;
      $long = array_key_exists('longitude', $row) ? $row['longitude'] : null;
      $alt = array_key_exists('altitude', $row) ? $row['altitude'] : null;

      $geo = $this->createGeo($study, $name, $lat, $long, $alt, $geoTypeName, $assignedId, $assignedParentId);
      array_push($geoIds, $geo->id);

      $row = $fileReader->getNextRowHash();
    }
    return $geoIds;
  }

  function createGeoType (String $name, Study $study, String $parentId = null, bool $canUserAdd = true, bool $canUserAddChild = true, bool $canContainRespondent = true): GeoType {
    $geoType = GeoType::firstOrNew(['name' => $name], [
      'id' => Uuid::uuid4(),
      'name' => $name,
      'study_id' => $study->id,
      'parent_id' => $parentId,
      'can_user_add' => $canUserAdd,
      'can_user_add_child' => $canUserAddChild,
      'can_contain_respondent' => $canContainRespondent
    ]);
    $geoType->save();
    return $geoType;
  }

  function createGeo (Study $study, String $name, $latitude, $longitude, $altitude, String $geoTypeName, String $assignedId, String $parentId = null): Geo {
    $nameTranslation = TranslationService::createTranslationForDefault($name, $study); // TODO: Create this translation
    $geoType = $this->createGeoType($geoTypeName, $study);
    $parent = Geo::where('assigned_id', $parentId)->orWhere('id', $parentId)->first();
    $geo = new Geo;
    $geo->id = Uuid::uuid4();
    $geo->assigned_id = $assignedId;
    $geo->name_translation_id = $nameTranslation->id;
    $geo->latitude = $latitude;
    $geo->longitude = $longitude;
    $geo->altitude = $altitude;
    $geo->geo_type_id = $geoType->id;
    if (isset($parent)) {
      $geo->parent_id = $parent->id;
    } else if (isset($parentId) && !isset($parent)) {
      throw new Exception('Unable to find parent matching this assigned id');
    }
    $geo->save();
    return $geo;
  }


  public static function importGeoPhotos (String $zipPath, String $studyId): int {
    $nPhotos = 0;
    $zip = new ZipArchive;
    if ($zip->open($zipPath) === TRUE) {
      $movedFiles = [];
      try {
        for ($i = 0; $i < $zip->numFiles; $i++) {
          $nPhotos++;
          $fileName = $zip->getNameIndex($i);
          $fileInfo = pathinfo($fileName);
          // TODO: Consider supporting other image types here
          if ($fileInfo['extension'] === 'jpg') {
            Log::info('JPG: ' . $fileInfo['basename']);
            $assignedId = $fileInfo['filename'];

            $geo = Geo::where('assigned_id', $assignedId)->orWhere('id', $assignedId)
              ->whereIn('geo_type_id', function ($q) use ($studyId) {
                return $q->select('id')
                  ->from('geo_type')
                  ->where('study_id', $studyId);
              })
              ->first();

            if (!isset($geo)) {
              throw new Exception("Unable to find respondent with assigned id matching file name $assignedId");
            }

            $photo = new Photo;
            $photo->id = Uuid::uuid4();
            $photo->file_name = Uuid::uuid4() . '.jpg';
            $photo->save();

            $respondentPhoto = new GeoPhoto;
            $respondentPhoto->id = Uuid::uuid4();
            $respondentPhoto->photo_id = $photo->id;
            $respondentPhoto->respondent_id = $geo->id;
            $respondentPhoto->sort_order = 0;
            $respondentPhoto->save();

            // Copy the files from the zip archive into the respondent_photos directory
            $newLocation = storage_path("respondent-photos/$photo->file_name");
            FileService::copyFromZip($zip, $fileName, $newLocation);
            array_push($movedFiles, $newLocation);
          }
        }
        $zip->close();
      } catch (Throwable $e) {
        // Cleanup any files that have already been moved
        foreach ($movedFiles as $path) {
          unlink($path);
        }
        throw $e;
      }
      return $nPhotos;
    } else {
      throw new Exception('Unable to open zip file');
    }
  }

}