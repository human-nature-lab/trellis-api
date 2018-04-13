<?php

namespace app\Http\Controllers;

use App\Models\Snapshot;
use App\Models\Device;
use Laravel\Lumen\Routing\Controller;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SyncControllerV2 extends Controller
{
    public function authenticate(Request $request, $deviceId) {
        $validator = Validator::make(array_merge($request->all(), [
            'id' => $deviceId
        ]), [
            'id' => 'required|string|exists:device,device_id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], Response::HTTP_UNAUTHORIZED);
        }

        $device = Device::where('device_id', $deviceId)->get();

        if (count($device) === 0) {
            return response()->json([
                'msg' => 'URL resource not found'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json([], Response::HTTP_OK);
    }

    public function getSnapshotInfo(Request $request, $deviceId) {
        $validator = Validator::make(array_merge($request->all(), [
            'id' => $deviceId
        ]), [
            'id' => 'required|string|exists:device,device_id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $latestSnapshot = Snapshot::where('deleted_at',null)
            ->orderBy('created_at', 'desc')
            ->first();

        return response()->json($latestSnapshot, Response::HTTP_OK);
    }
}
