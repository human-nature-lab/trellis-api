<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;
use App\Models\Geo;
use App\Library\TranslationHelper;
use Ramsey\Uuid\Uuid;
use DB;

class GeoController extends Controller
{
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

        $geoModel = Geo::select('geo.id', 'gt.name AS type_name', 'gt.id as geo_type_id', 'geo.parent_id', 'geo.latitude', 'geo.longitude', 'geo.altitude', 'tt.translated_text AS name')
            ->join('translation_text AS tt', 'tt.translation_id', '=', 'geo.name_translation_id')
            ->join('geo_type AS gt', 'gt.id', '=', 'geo.geo_type_id')
            ->where('tt.locale_id', $localeId)
            ->limit($limit)
            ->offset($offset)
            ->get();

        return response()->json(
            ['geos' => $geoModel],
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
            ['geos' => $geoModel],
            Response::HTTP_OK
        );
    }

    public function updateGeo(Request $request, $id)
    {
        $validator = Validator::make(array_merge($request->all(), [
            'id' => $id
        ]), [
            'id' => 'required|string|min:36',
            'geo_type_id' => 'string|min:36',
            'parent_id' => 'string|min:36',
            'latitude' => 'integer|min:1',
            'longitude' => 'integer|min:1',
            'altitude' => 'integer|min:1',
            'name_translation_id' => 'string|min:36'
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

    public function removeGeo(Request $request, $id)
    {
        $validator = Validator::make(
            ['id' => $id],
            ['id' => 'required|string|min:36']
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
                'msg' => 'URL resource was not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $geoModel->delete();

        return response()->json([

        ]);
    }

    public function createGeo(Request $request, $localeId)
    {
        $validator = Validator::make(array_merge($request->all(), [
            'localeId' => $localeId
        ]), [
            'localeId' => 'required|string|min:36|exists:locale,id',
            'geo_type_id' => 'required|string|min:36|exists:geo_type,id',
            'parent_id' => 'string|min:36|exists:geo,id',
            'latitude' => 'string|min:1',
            'longitude' => 'string|min:1',
            'altitude' => 'string|min:1',
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
}
