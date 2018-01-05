<?php

namespace app\Http\Controllers;

use App\Library\DatabaseHelper;
use App\Library\FileHelper;
use App\Models\Device;
use App\Models\Epoch;
use App\Models\Log;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Laravel\Lumen\Routing\Controller;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use RuntimeException;
use Symfony\Component\Finder\Finder;
use Validator;

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
            \Log::debug($insertQuery);
            \Log::debug(implode(", ", array_values($row)));
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

        ob_end_clean(); // disable Lumen's output buffering in order to allow infinite response length without using up memory

        http_response_code(Response::HTTP_OK);

        $response = app()->handle(Request::create(app('request')->getRequestURI(), app('request')->getMethod()));   // get original response headers for cookies, CORS, etc

        foreach(explode("\r\n", $response->headers) as $header) {
            header($header);
        }

        header('Content-Type: ' . response()->json()->headers->get('content-type'));    // override content type to ensure that it's application/json

        echo '[';

        $path = storage_path() . '/respondent-photos';
        $extensions = ['jpg'];//['jpg', 'gif', 'png'];
        $pattern = '/\.(' . implode('|', array_map('preg_quote', $extensions, array_fill(0, count($extensions), '/'))) . ')$/';
        $first = true;

        foreach ((new Finder())->name($pattern)->files()->in($path) as $file) {
            if($first) {
                $first = false;
            } else {
                echo ',';
            }

            echo json_encode([
                "fileName" => $file->getFilename(),
                "length" => $file->getSize()
            ]);
        }

        echo ']';

        // // original method
        // $returnArray = array();
        // $adapter = new Local(storage_path() . '/respondent-photos');
        // $filesystem = new Filesystem($adapter);
        // $contents = $filesystem->listContents();
        //
        // foreach ($contents as $object) {
        //     if ($object['extension'] == "jpg") {
        //         $returnArray[] = array('fileName' => $object['path'], 'length' => $object['size']);
        //     }
        // }
        //
        // return response()->json($returnArray, Response::HTTP_OK);
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

    public function uploadSync(Request $request, $deviceId)
    {
        $validator = Validator::make(array_merge($request->all(), [
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

        ob_start();

        $resultCode = Artisan::call('trellis:import:snapshot');

        $result = json_decode(ob_get_clean(), true);

        if ($resultCode != 0) {
            return response()->json($result, Response::HTTP_BAD_REQUEST); // if any inconsistent rows, return them with error code and roll back transaction
            // \App::abort(Response::HTTP_CONFLICT, json_encode($inconsistencies, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));    // message doesn't work
            // throw new \Exception('Foreign key consistency check failed for the following tables: ' . implode(', ', array_keys($inconsistencies)));
        }

        $epoch = $result > 0 ? Epoch::inc() : Epoch::get();    // increment epoch if any rows were written or logged

        Device::where('device_id', $deviceId)
            ->update([
                'epoch' => $epoch
            ]);

        return response()->json([]/*$writes*/, Response::HTTP_OK); // now <name_greater_than_or_equal_to_epoch>.sqlite.sql.zip must exist in order to download snapshot, otherwise client must wait for next snapshot to be generated
    }

    public function downloadSync(Request $request, $deviceId)
    {
        $validator = Validator::make(array_merge($request->all(), [
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

        try {
            Artisan::call('trellis:export:snapshot');
        } catch (RuntimeException $e) {
            // WithoutOverlapping trait throws RuntimeException('Command is running now!') if command is already running
        }

        app()->configure('snapshot');   // save overhead by only loading config when needed

        $snapshotDirPath = FileHelper::storagePath(config('snapshot.directory.path'));
        $files = glob("$snapshotDirPath/*");

        if (count($files)) {
            $files = array_combine($files, array_map("filemtime", $files));
            $newestFilePath = array_keys($files, max($files))[0];
            $newestFileName = basename($newestFilePath);
            $hex = explode('.', $newestFileName)[0];
            $snapshotEpoch = Epoch::dec($hex)*1;
            $deviceEpoch = Device::where('device_id', $deviceId)->first()->epoch;

            if ($snapshotEpoch >= $deviceEpoch) {
                return response()->download($newestFilePath, $newestFileName);   // if snapshot epoch >= than device's epoch, then return binary file download
            }
        }

        return response()->json([], Response::HTTP_ACCEPTED);   // if no snapshot epoch >= than device's epoch, then return 202 Accepted to make client retry later
    }
}
