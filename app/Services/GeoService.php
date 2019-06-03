<?php

namespace App\Services;

use App\Library\CsvFileReader;
use App\Models\Geo;
use App\Models\GeoType;
use App\Models\Study;
use Ramsey\Uuid\Uuid;
use Exception;

class GeoService {

  function importGeosFromFile (String $filePath, String $studyId): array {

    $fileReader = new CsvFileReader($filePath);
    $fileReader->open();

    $row = $fileReader->getNextRowHash();

    $study = Study::find($studyId);

    if (!isset($study)) {
      throw new Exception('No study with this idea exists');
    }

    while ($row !== null) {

      $id = Uuid::uuidv4();
      $name = $row['name'];
      $assignedId = $row['id'];
      $geoTypeName = $row['geo_type'];
      $assignedParentId = $row['parent_id'];
      $lat = $row['latitude'];
      $long = $row['longitutde'];
      $alt = $row['altitude'];

      $this->createGeo($study, $name, $lat, $long, $alt, $geoTypeName, )

      $row = $fileReader->getNextRowHash();
    }
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
    $geo = new Geo;
    $geo->id = Uuid::uuid4();
    $geo->name_translation_id = $nameTranslation->id;
    $geo->latitude = $latitude;
    $geo->longitude = $longitude;
    $geo->altitude = $altitude;
  }

}