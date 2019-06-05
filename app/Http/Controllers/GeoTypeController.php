<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;
use App\Models\GeoType;
use App\Models\Geo;
use Ramsey\Uuid\Uuid;

class GeoTypeController extends Controller
{
    public function getGeoType(Request $request, $id)
    {
        $validator = Validator::make(
            ['id' => $id],
            ['id' => 'required|string|min:36|exists:geo_type,id']
        );

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $geoTypeModel = GeoType::find($id);

        if ($geoTypeModel === null) {
            return response()->json([
                'msg' => 'URL resource not found'
            ], Response::HTTP_OK);
        }

        return response()->json([
            'geoType' => $geoTypeModel
        ], Response::HTTP_OK);
    }

    public function getGeoTypes(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'study_id' => 'required|string|min:36|exists:study,id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $studyId = $request->query('study_id');
        $getUserAddable = $request->query('get_user_addable', 'false');

        $q = GeoType::where('study_id', $studyId);
        if ($getUserAddable == 'true') {
            $q = $q->where('can_user_add', 1);
        }

        $geoTypes = $q->orderBy('name', 'asc')->get();

        return response()->json(
            ['geoTypes' => $geoTypes],
            Response::HTTP_OK
        );
    }

    public function getAllGeoTypesByStudyId($studyId)
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

        $geoTypeModel = GeoType::where('study_id', $studyId)
            ->orderBy('name', 'asc')
            ->get();

        return response()->json(
            ['geo_types' => $geoTypeModel],
            Response::HTTP_OK
        );
    }

    public function getAllGeoTypes(Request $request)
    {
        $geoTypeModel = GeoType::orderBy('name', 'asc')
            ->get();

        return response()->json(
            ['geo_types' => $geoTypeModel],
            Response::HTTP_OK
        );
    }

    public function getAllEligibleGeoTypesOfParentGeo($parent_geo_id)
    {
        $geoModel = Geo::where('geo.id', $parent_geo_id)
                ->first();
        $geoTypeModel = GeoType::where('geo_type.parent_id', $geoModel->geo_type_id)
                ->get();

        return response()->json([
            'geoTypes' => $geoTypeModel
        ], Response::HTTP_OK);
    }

    public function updateGeoType(Request $request, $id)
    {
        $validator = Validator::make(array_merge($request->all(), [
            'id' => $id
        ]), [
            'id' => 'string|min:36|exists:geo_type,id',
            'parent_id' => 'nullable|string|min:36',
            'name' => 'nullable|string|min:1',
            'can_user_add' => 'boolean',
            'can_user_add_child' => 'boolean',
            'can_contain_respondent' => 'boolean'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors(),
            ], $validator->statusCode());
        }

        $geoTypeModel = GeoType::find($id);

        if ($geoTypeModel === null) {
            return response()->json([
                'msg' => 'URL resource not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $geoTypeModel->fill($request->all());
        $geoTypeModel->save();

        return response()->json([
            'geo_type' => $geoTypeModel
        ], Response::HTTP_OK);
    }

    public function removeGeoType($geo_type_id)
    {
        $validator = Validator::make(
            ['id' => $geo_type_id],
            ['id' => 'required|string|min:36|exists:geo_type,id']
        );

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $geoTypeModel = GeoType::find($geo_type_id);

        if ($geoTypeModel === null) {
            return response()->json([
                'msg' => 'URL resource was not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $geoTypeModel->delete();

        return response()->json([

        ]);
    }

    public function createGeoType(Request $request, $studyId)
    {
        $validator = Validator::make(array_merge(['study_id' => $studyId], $request->all()), [
            'study_id' => 'string|min:36|exists:study,id',
            'parent_id' => 'nullable|string|min:36',
            'name' => 'string|min:1',
            'can_user_add' => 'boolean',
            'can_contain_respondent' => 'boolean',
            'can_user_add_child' => 'boolean'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $newGeoTypeModel = new GeoType;

        $newGeoTypeModel->fill($request->all());
        $newGeoTypeModel->id = Uuid::uuid4();
        $newGeoTypeModel->study_id = $studyId;
        $newGeoTypeModel->save();

        return response()->json([
            'geo_type' => $newGeoTypeModel
        ], Response::HTTP_OK);
    }
}
