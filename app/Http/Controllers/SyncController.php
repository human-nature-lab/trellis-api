<?php

namespace app\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller;
use Validator;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use Log;

class SyncController extends Controller
{
    public function heartbeat()
    {
        return response()->json([], Response::HTTP_OK);
    }

    public function store(Request $request, $deviceId)
    {
        $validator = Validator::make(array_merge($request->all(), [
            'id' => $deviceId
        ]), [
            'id' => 'required|string|min:36|exists:device,device_id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }
    }

    public function download(Request $request, $deviceId)
    {
        $validator = Validator::make(array_merge($request->all(), [
            'id' => $deviceId
        ]), [
            'id' => 'required|string|min:14|exists:device,device_id',
            'table' => 'required|string|min:1'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        };

        $response = [];

        $response["deviceId"] = $deviceId;
        $response["table"] = $request->input('table');

        $tableClass = str_replace(' ', '', ucwords(str_replace('_', ' ', $request->input('table'))));
        $className = "\\App\\Models\\$tableClass";
        $classModel = $className::all();

        $response["numRows"] = $classModel->count();
        $response["totalRows"] = $classModel->count();
        $response["rows"] = $classModel;

        return response()->json($response, Response::HTTP_OK);
    }

    public function upload(Request $request, $deviceId)
    {
        $validator = Validator::make(array_merge($request->all(), [
            'id' => $deviceId
        ]), [
            'id' => 'required|string|min:14|exists:device,device_id',
            'table' => 'required|string|min:1'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        };

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        foreach ($request->input('rows') as $row) {
            /*
            $newClassName = "\\App\\Models\\" . str_replace(' ', '', str_replace('_', '', ucwords($request->input('table'), '_')));
            $newClassName::create($row);
            */
            // Need to INSERT IGNORE to allow for resuming incomplete syncs
            $fields = implode(',', array_keys($row));
            $values = '?' . str_repeat(',?', count($row) - 1);
            $insertQuery = 'insert ignore into ' . $request->input('table') . ' (' . $fields . ') values (' . $values . ')';
            Log::debug($insertQuery);
            Log::debug(implode(", ", array_values($row)));
            DB::insert($insertQuery, array_values($row));
        }
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        return response()->json([], Response::HTTP_OK);
    }

    public function listImages($deviceId)
    {
        //the fields are fileName:<string>, deviceId:<string>, action:<string>, length:<number>,base64:<string/base64>. Note that base64 uses no linefeeds
        $validator = Validator::make(array_merge([], [
            'id' => $deviceId
        ]), [
            'id' => 'required|string|min:14|exists:device,device_id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        };

        $returnArray = array();
        $adapter = new Local(storage_path() . '/respondent-photos');
        $filesystem = new Filesystem($adapter);

        $contents = $filesystem->listContents();

        foreach ($contents as $object) {
            if ($object['extension'] == "jpg") {
                $returnArray[] = array('fileName' => $object['path'], 'length' => $object['size']);
            }
        }

        return response()->json($returnArray, Response::HTTP_OK);
    }

    public function syncImages(Request $request, $deviceId)
    {
        //the fields are fileName:<string>, deviceId:<string>, action:<string>, length:<number>,base64:<string/base64>. Note that base64 uses no linefeeds
        $validator = Validator::make(array_merge($request->all(), [
            'id' => $deviceId
        ]), [
            'id' => 'required|string|min:14|exists:device,device_id',
            'fileName' => 'required|string|min:1',
            'action' => 'required|string|min:1',
            'base64' => 'string'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        };

        if ($request->input('action') == 'up') {
            // TODO: replace hard-coded directory with config / env variable
            $adapter = new Local(storage_path() . '/respondent-photos');
            $filesystem = new Filesystem($adapter);
            $data = base64_decode($request->input('base64'));
            $filesystem->put($request->input('fileName'), $data);
        } else {
            $adapter = new Local(storage_path() . '/respondent-photos');
            $filesystem = new Filesystem($adapter);
            $exists = $filesystem->has($request->input('fileName'));
            if ($exists) {
                $contents = $filesystem->read($request->input('fileName'));
                $size = $filesystem->getSize($request->input('fileName'));

                $base64 = base64_encode($contents);

                return response()->json([
                    'fileName' => $request->input('fileName'),
                    'device_id' => $deviceId,
                    'length' => $size,
                    'base64' => $base64],
                    Response::HTTP_OK);
            }
        }
    }
}
