<?php

namespace App\Http\Controllers;

use App\Models\GeoPhoto;
use App\Services\GeoService;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Log;
use Throwable;
use Validator;
use App\Models\Geo;
use App\Library\TranslationHelper;
use Ramsey\Uuid\Uuid;

class GeoController extends Controller
{

  public function searchGeos(Request $request) {

    $query = $request->get('query');
    $limit = $request->get('limit', 1000);
    $offset = $request->get('offset', 0);
    $study = $request->get('study');
    $parent = $request->get('parent');
    $onlyNoParent = $request->get('no-parent');
    $types = $request->get('types');
    $types = isset($types) ? explode(',', $types) : [];

    $validator = Validator::make([
      'limit' => $limit,
      'offset' => $offset,
      'parent' => $parent,
      'study' => $study,
      'types' => $types
    ], [
      'limit' => 'nullable|max:100',
      'offset' => 'nullable|min:0',
      'study' => 'nullable|exists:study,id',
      'parent' => 'nullable|exists:geo,id',
      'type' => 'nullable|array|exists:geo_type,id'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'msg' => $validator->errors()
      ], Response::HTTP_BAD_REQUEST);
    }


    $q = Geo::with('photos', 'nameTranslation', 'geoType');
    if (isset($query)) {
      $terms = explode(" ", $query);
      Log::debug(json_encode($terms));
      foreach ($terms as $i => $term) {
        $q = $q->whereIn('name_translation_id', function ($sq) use ($term) {
          $sq->select('translation_id')
            ->from('translation_text')
            ->where('translated_text', 'like', "%$term%");
        });
      }
    }

    if (isset($study)) {
      $q = $q->whereIn('geo_type_id', function ($sq) use ($study) {
        $sq->select('id')
          ->from('geo_type')
          ->where('study_id', '=', $study);
      });
    }

    if (isset($parent)) {
      $q = $q->where('parent_id', '=', $parent);
    } else if (isset($onlyNoParent)) {
      $q = $q->whereNull('parent_id');
    }

    if (count($types) > 0) {
      $q = $q->whereIn('geo_type_id', $types);
    }

    $q = $q->take($limit)->skip($offset);

    Log::debug($q->toSql());

    $geos = $q->get();

    return response()->json([
      'geos' => $geos
    ], Response::HTTP_OK);

  }

  public function importGeos (Request $request, GeoService $geoService, String $studyId) {
    $validator = Validator::make(array_merge($request->all(), [
      'studyId' => $studyId
    ]), [
      'studyId' => 'required|string|min:36|exists:study,id'
    ]);

    if ($validator->fails() === true) {
      return response()->json([
        'msg' => $validator->errors()
      ], $validator->statusCode());
    }

    $hasFile = $request->hasFile('file');
    if ($hasFile) {
      $file = $request->file('file');

      try {
        DB::beginTransaction();
        $importedGeoIds = $geoService->importGeosFromFile($file->getRealPath(), $studyId);
        DB::commit();
      } catch (Throwable $e) {
        Log::error($e);
        DB::rollback();
        throw $e;
        return response()->json([
          'err' => 'Please provide a file in the correct format.'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
      }

      return response()->json([
        'geos' => Geo::with('photos', 'nameTranslation')->whereIn('id', $importedGeoIds)->get()
      ], Response::HTTP_OK);
    } else {
      return response()->json([
        'msg' => 'Request failed. Please provide a file in the correct format.',
      ], Response::HTTP_BAD_REQUEST);
    }
  }

  public function importGeoPhotos (Request $request, GeoService $geoService, String $studyId) {

    $validator = Validator::make(array_merge($request->all(), [
      'studyId' => $studyId
    ]), [
      'studyId' => 'required|string|min:36|exists:study,id'
    ]);

    if ($validator->fails() === true) {
      return response()->json([
        'msg' => 'Validation failed',
        'err' => $validator->errors()
      ], $validator->statusCode());
    }

    $hasFile = $request->hasFile('file');
    if ($hasFile) {
      try {
        DB::beginTransaction();
        $file = $request->file('file');
        $nGeoPhotos = $geoService->importGeoPhotos($file->getRealPath(), $studyId);
        DB::commit();
        return response()->json([
          'importedGeoPhotos' => $nGeoPhotos
        ], Response::HTTP_OK);
      } catch (Throwable $e) {
        DB::rollBack();
        return response()->json([
          'msg' => $e->getMessage()
        ], Response::HTTP_BAD_REQUEST);
      }
    } else {
      return response()->json([
        'msg' => 'Request failed',
        'err' => 'Provide a zip file with valid image names and types.'
      ], Response::HTTP_BAD_REQUEST);
    }

  }

  public function getGeo(Request $request, $id)
  {
    $validator = Validator::make(
      ['id' => $id],
      ['id' => 'required|string|min:36|exists:geo,id']
    );

    if ($validator->fails() === true) {
      return response()->json([
        'msg' => 'Validation failed',
        'err' => $validator->errors()
      ], $validator->statusCode());
    }

    $geoModel = Geo::find($id);

    if ($geoModel === null) {
      return response()->json([
        'msg' => 'URL resource not found'
      ], Response::HTTP_OK);
    }

    return response()->json([
      'geo' => $geoModel
    ], Response::HTTP_OK);
  }

  public function deleteGeoPhoto($geoPhotoId)
  {
    $validator = Validator::make([
      'geoPhotoId' => $geoPhotoId
    ], [
      'geoPhotoId' => 'required|string|min:36|exists:geo_photo,id'
    ]);

    if ($validator->fails() === true) {
      return response()->json([
        'msg' => 'Validation failed',
        'err' => $validator->errors()
      ], $validator->statusCode());
    }

    GeoPhoto::where('id', $geoPhotoId)->delete();

    return response()->json([], Response::HTTP_OK);
  }

  public function getGeoPhotos($geoId)
  {
    $geoId = urldecode($geoId);
    $validator = Validator::make([
      'geoId' => $geoId
    ], [
      'geoId' => 'required|string|min:32|exists:geo,id'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'msg' => "Invalid respondent id",
        'err' => $validator->errors()
      ], $validator->statusCode());
    }

    $photos = GeoPhoto::with('photo')->where('geo_id', $geoId)->get();

    return response()->json([
      'photos' => $photos
    ], Response::HTTP_OK);
  }

  public function updateGeoPhotos(Request $request)
  {
    $validator = Validator::make($request->all(),
      [
        'photos' => 'required|array'
      ]);

    if ($validator->fails() === true) {
      return response()->json([
        'msg' => 'Validation failed',
        'err' => $validator->errors()
      ], $validator->statusCode());
    }

    $photos = $request->input('photos');

    foreach ($photos as $photo) {
      $geoPhoto = GeoPhoto::find($photo['pivot']['id']);

      if ($geoPhoto !== null) {
        // Update sort order and notes
        $geoPhoto->sort_order = $photo['pivot']['sortOrder'];
        $geoPhoto->notes = $photo['pivot']['notes'];
        $geoPhoto->save();
      }
    }

    return response()->json([], Response::HTTP_OK);
  }

  public function getAllGeos(Request $request, $localeId)
  {
    $validator = Validator::make(array_merge($request->all(), [
      'localeId' => $localeId
    ]), [
      'localeId' => 'required|string|min:36|exists:locale,id'
    ]);

    // Default to limit = 100 and offset = 0
    $limit = $request->input('limit', 100);
    $offset = $request->input('offset', 0);

    if ($validator->fails() === true) {
      return response()->json([
        'msg' => 'Validation failed',
        'err' => $validator->errors()
      ], $validator->statusCode());
    }

    $count = Geo::count();

    $geoModel = Geo::select('geo.id', 'gt.name AS type_name', 'gt.id as geo_type_id', 'geo.parent_id', 'geo.latitude', 'geo.longitude', 'geo.altitude', 'tt.translated_text AS name')
      ->join('translation_text AS tt', 'tt.translation_id', '=', 'geo.name_translation_id')
      ->join('geo_type AS gt', 'gt.id', '=', 'geo.geo_type_id')
      ->where('tt.locale_id', $localeId)
      ->limit($limit)
      ->offset($offset)
      ->get();

    return response()->json(
      ['geos' => $geoModel,
        'limit' => $limit,
        'offset' => $offset,
        'count' => $count],
      Response::HTTP_OK
    );
  }

  public function getGeoCountByStudyId(Request $request, $studyId)
  {
    $validator = Validator::make(
      ['study_id' => $studyId],
      ['study_id' => 'required|string|min:36|exists:study,id']
    );

    if ($validator->fails() === true) {
      return response()->json([
        'msg' => 'Validation failed',
        'err' => $validator->errors()
      ], $validator->statusCode());
    }

    $count = Geo::whereRaw('geo_type_id in (select id from geo_type where study_id = ?)')
      ->setBindings([$studyId])
      ->count();

    return response()->json(
      ['count' => $count],
      Response::HTTP_OK
    );
  }

  public function getAllGeosByStudyId(Request $request, $studyId)
  {
    $validator = Validator::make(
      ['study_id' => $studyId],
      ['study_id' => 'required|string|min:36|exists:study,id']
    );

    // Default to limit = 100 and offset = 0
    $limit = $request->input('limit', 100);
    $offset = $request->input('offset', 0);

    if ($validator->fails() === true) {
      return response()->json([
        'msg' => 'Validation failed',
        'err' => $validator->errors()
      ], $validator->statusCode());
    }

    //$geoModel = DB::table('geo')
    $count = Geo::whereRaw('geo_type_id in (select id from geo_type where study_id = ?)')
      ->setBindings([$studyId])
      ->count();

    $geoModel = Geo::with('nameTranslation', 'geoType', 'parent')
      ->whereRaw('geo_type_id in (select id from geo_type where study_id = ?)')
      ->setBindings([$studyId])
      ->limit($limit)
      ->offset($offset)
      ->get();
    //->toSql();

    //$geoModel->load('nameTranslation');

    //\Log::info('Query: ' . $geoModel);

    return response()->json(
      ['geos' => $geoModel,
        'limit' => $limit,
        'offset' => $offset,
        'count' => $count],
      Response::HTTP_OK
    );
  }

  public function updateGeo(Request $request, $id)
  {
    $validator = Validator::make(array_merge($request->all(), [
      'id' => $id
    ]), [
      'id' => 'required|string|min:36',
      'geo_type_id' => 'nullable|string|min:36',
      'parent_id' => 'nullable|string|min:36',
      'latitude' => 'nullable|numeric',
      'longitude' => 'nullable|numeric',
      'altitude' => 'nullable|numeric',
      'name_translation_id' => 'nullable|string|min:36'
    ]);

    if ($validator->fails() === true) {
      return response()->json([
        'msg' => 'Validation failed',
        'err' => $validator->errors()
      ], $validator->statusCode());
    }

    $geoModel = Geo::find($id);

    if ($geoModel === null) {
      return response()->json([
        'msg' => 'URL resource not found'
      ], Response::HTTP_NOT_FOUND);
    }

    $geoModel->fill($request->input());
    $geoModel->save();

    return response()->json([
      'msg' => Response::$statusTexts[Response::HTTP_OK]
    ], Response::HTTP_OK);
  }

  public function removeGeo($geo_id)
  {
    $validator = Validator::make(
      ['id' => $geo_id],
      ['id' => 'required|string|min:36']
    );

    if ($validator->fails() === true) {
      return response()->json([
        'msg' => 'Validation failed',
        'err' => $validator->errors()
      ], $validator->statusCode());
    }

    $geoModel = Geo::find($geo_id);

    if ($geoModel === null) {
      return response()->json([
        'msg' => 'URL resource was not found'
      ], Response::HTTP_NOT_FOUND);
    }

    $geoModel->delete();

    return response()->json([

    ]);
  }

  public function moveGeo(Request $request, $geo_id)
  {
    $validator = Validator::make(
      ['id' => $geo_id],
      ['id' => 'required|string|min:36']
    );

    if ($validator->fails() === true) {
      return response()->json([
        'msg' => 'Validation failed',
        'err' => $validator->errors()
      ], $validator->statusCode());
    }

    $latitude = $request->get('latitude');
    $longitude = $request->get('longitude');
    $moveChildren = $request->get('moveChildren');

    $q = Geo::where('id', '=', $geo_id);
    if ($moveChildren) {
      $q = $q->orWhere('parent_id', '=', $geo_id);
    }
    $q->update([
      'latitude' => $latitude,
      'longitude' => $longitude
    ]);

    return response()->json([
    ]);
  }

  public function createGeoFromModel(Request $request) {
    $altitude = $request->input('geo.altitude');
    $longitude = $request->input('geo.longitude');
    $latitude = $request->input('geo.latitude');
    $geoTypeId = $request->input('geo.geoType.id');
    $parentId = $request->input('geo.parentId');
    $translationTextArray = $request->input('geo.nameTranslation.translationText');

    $validator = Validator::make([
      'longitude' => $longitude,
      'latitude' => $latitude,
      'geoTypeId' => $geoTypeId,
      'parentId' => $parentId,
      'translationTextArray' => $translationTextArray
    ], [
      'longitude' => 'required|numeric',
      'latitude' => 'required|numeric',
      'geoTypeId' => 'required|string|min:36|exists:geo_type,id',
      'parentId' => 'nullable|string|min:36|exists:geo,id',
      'translationTextArray' => 'array'
    ]);

    if ($validator->fails() === true) {
      return response()->json([
        'msg' => 'Validation failed',
        'err' => $validator->errors()
      ], $validator->statusCode());
    }

    $newGeoModel = new Geo;
    $geoId = Uuid::uuid4();

    DB::transaction(function () use ($newGeoModel, $geoId, $altitude, $longitude, $latitude, $geoTypeId, $parentId, $translationTextArray) {
      $newGeoModel->id = $geoId;
      $newGeoModel->geo_type_id = $geoTypeId;
      $newGeoModel->parent_id = $parentId;
      $newGeoModel->latitude = $latitude;
      $newGeoModel->longitude = $longitude;
      $newGeoModel->altitude = $altitude;
      $newGeoModel->name_translation_id = TranslationHelper::createNewTranslationFromTranslationTextArray($translationTextArray);
      $newGeoModel->save();
    });

    return response()->json([
      'geo' => $newGeoModel
    ], Response::HTTP_OK);
  }

  public function createGeo(Request $request, $localeId)
  {
    $validator = Validator::make(array_merge($request->all(), [
      'localeId' => $localeId
    ]), [
      'localeId' => 'required|string|min:36|exists:locale,id',
      'geo_type_id' => 'required|string|min:36|exists:geo_type,id',
      'parent_id' => 'nullable|string|min:36|exists:geo,id',
      'latitude' => 'nullable|string|min:1',
      'longitude' => 'nullable|string|min:1',
      'altitude' => 'nullable|string|min:1',
      'name' => 'required|string|min:1'
    ]);

    if ($validator->fails() === true) {
      return response()->json([
        'msg' => 'Validation failed',
        'err' => $validator->errors()
      ], $validator->statusCode());
    }

    $newGeoModel = new Geo;
    $geoId = Uuid::uuid4();

    DB::transaction(function () use ($request, $newGeoModel, $localeId, $geoId) {
      $geoTypeId = $request->input('geo_type_id');
      $parentId = $request->input('parent_id');
      $name = $request->input('name');
      $latitude = $request->input('latitude');
      $longitude = $request->input('longitude');
      $altitude = $request->input('altitude');

      $newGeoModel->id = $geoId;
      $newGeoModel->geo_type_id = $geoTypeId;
      $newGeoModel->parent_id = $parentId;
      $newGeoModel->latitude = $latitude;
      $newGeoModel->longitude = $longitude;
      $newGeoModel->altitude = $altitude;
      $newGeoModel->name_translation_id = TranslationHelper::createNewTranslation($name, $localeId);
      $newGeoModel->save();
    });

    $returnGeo = Geo::with('parent', 'nameTranslation', 'geoType')->find($geoId);

    return response()->json([
      'geo' => $returnGeo
    ], Response::HTTP_OK);
  }

  /**
   * Get a bunch of geos at one time by passing in a list of comma delimited ids. If any of the ids are invalid the
   * entire request will fail.
   * @param {String} $ids - A comma delimited list of geo ids
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function getGeosById ($ids) {
    $geoIds = explode(',', $ids);
    $geoIds = array_map(function ($id) {
      return urldecode($id);
    }, $geoIds);
    $validator = Validator::make([
      'geo_ids' => $geoIds
    ], [
      'geo_ids' => 'required|exists:geo,id'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'err' => $validator->errors()
      ], $validator->statusCode());
    }

    $geos = Geo::with('photos', 'nameTranslation', 'geoType')->whereIn('id', $geoIds)->get();

    return response()->json([
      'geos' => $geos
    ], Response::HTTP_OK);
  }

  public function getGeosByParentId ($studyId, $parentId) {

    if ($parentId === 'null') {
      $parentId = null;
    }

    $validator = Validator::make([
      'studyId' => $studyId,
      'parentId' => $parentId
    ], [
      'studyId' => 'string|min:36|exists:study,id',
      'parentId' => 'nullable|string|min:36|exists:geo,id'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'msg' => $validator->errors()
      ], $validator->statusCode());
    }

    $geos = Geo::with('photos', 'nameTranslation', 'geoType')
      ->whereIn('geo_type_id', function ($q) use ($studyId) {
        return $q->select('id')
          ->from('geo_type')
          ->where('geo_type.study_id', '=', $studyId);
      });
    if (!isset($parentId)) {
      $geos = $geos->whereNull('geo.parent_id');
    } else {
      $geos = $geos->where('geo.parent_id', '=', $parentId);
    }
    return response()->json([
      'geos' => $geos->get()
    ], Response::HTTP_OK);
  }

  /**
   * Get an array of all ancestors for a specific geoId (not exceeding 25 levels)
   * @param $geoId
   * @return \Illuminate\Http\JsonResponse
   */
  public function getAncestorsForGeoId ($geoId) {
    $geoId = urldecode($geoId);
    $validator = Validator::make([
      'geoId' => $geoId
    ], [
      'geoId' => 'required|string|min:36|exists:geo,id'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'msg' => $validator->errors()
      ], $validator->statusCode());
    }

    $ancestors = [];
    $c = 0;
    while ($geoId && !isset($ancestors[$geoId]) && $c < 25) {
      $geo = Geo::with('nameTranslation', 'geoType', 'photos')->find($geoId);
      $ancestors[$geo->id] = $geo;
      $geoId = $geo->parent_id;
    }

    return response()->json([
      'ancestors' => array_reverse(array_map(function ($i) {
        return $i;
      }, array_values($ancestors)))
    ], Response::HTTP_OK);
  }
}
