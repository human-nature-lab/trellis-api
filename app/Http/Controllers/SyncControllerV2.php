<?php

namespace app\Http\Controllers;

use App\Models\Snapshot;
use App\Models\Device;
use Laravel\Lumen\Routing\Controller;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

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

    public function getSnapshotFileSize(Request $request, $snapshotId) {
        $validator = Validator::make(array_merge($request->all(), [
            'id' => $snapshotId
        ]), [
            'id' => 'required|string|exists:snapshot,id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $snapshot = Snapshot::find($snapshotId);
        if ($snapshot === null) {
            return response()->json([
                'msg' => 'Snapshot ID not found.',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $adapter = new Local(storage_path() . '/snapshot');
        $filesystem = new Filesystem($adapter);
        $exists = $filesystem->has($snapshot->file_name);

        if (!$exists) {
            return response()->json([
                'msg' => 'Snapshot file not found.',
                'err' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $size = $filesystem->getSize($snapshot->file_name);

        return response()->json($size, Response::HTTP_OK);
    }


    public function downloadSnapshot(Request $request, $snapshotId) {
        $validator = Validator::make(array_merge($request->all(), [
            'id' => $snapshotId
        ]), [
            'id' => 'required|string|exists:snapshot,id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $snapshot = Snapshot::find($snapshotId);
        if ($snapshot === null) {
            return response()->json([
                'msg' => 'Snapshot ID not found.',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $adapter = new Local(storage_path() . '/snapshot');
        $filesystem = new Filesystem($adapter);
        $exists = $filesystem->has($snapshot->file_name);

        if (!$exists) {
            return response()->json([
                'msg' => 'Snapshot file not found.',
                'err' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->download(storage_path() . '/snapshot/' . $snapshot->file_name);
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
