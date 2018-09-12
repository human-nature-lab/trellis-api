<?php

namespace app\Http\Controllers;

use App\Models\Snapshot;
use App\Models\Device;

use Illuminate\Support\Facades\Artisan;

use App\Models\Upload;

use Laravel\Lumen\Routing\Controller;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use Emgag\Flysystem\Hash\HashPlugin;
use Symfony\Component\Finder\Finder;

use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Cached\Storage\Memory as MemoryStore;

use Ramsey\Uuid\Uuid;


class SyncControllerV2 extends Controller
{
    public function heartbeat()
    {
        return response()->json([], Response::HTTP_OK);
    }

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

    public function verifyUpload(Request $request, $deviceId) {
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

        $adapter = new Local(storage_path() . '/uploads');
        $filesystem = new Filesystem($adapter);
        $filesystem->addPlugin(new HashPlugin);
        $exists = $filesystem->has($request->get('fileName'));

        if (!$exists) {
            return response()->json([
                'msg' => 'Upload file not found.',
                'err' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $md5 = $filesystem->hash($request->get('fileName'), 'md5');

        if ($md5 <> $request->get('md5hash')) {
            return response()->json([
                'msg' => 'Calculated hash does not match provided hash.',
                'err' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $upload = new Upload;
        $upload->id = Uuid::uuid4();
        $upload->device_id = $deviceId;
        $upload->file_name = $request->get('fileName');
        $upload->hash = $request->get('md5hash');
        $upload->status = 'PENDING';
        $upload->save();

        return response()->json([], Response::HTTP_OK);
    }

    public function upload(Request $request, $deviceId) {
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

        if (! $request->hasFile('file')) {
            return response()->json([
                'msg' => 'File not present in request.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $file = $request->file('file');
        $fileName = $request->get('fileName');
        $uploadPath = storage_path() . '/uploads';

        if (! $request->file('file')->isValid()) {
            return response()->json([
                'msg' => 'Upload failed.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $file->move($uploadPath, $fileName);

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

    public function getPendingUploads(Request $request, $deviceId) {
        /* Returns both pending and error uploads to prevent end-users from downloading before their upload has been processed */
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

        $pendingUploads = Upload::where('deleted_at',null)
            ->where('status', 'PENDING')
            ->orWhere('status', 'ERROR')
            ->get();

        return response()->json($pendingUploads, Response::HTTP_OK);
    }

    public function getImageSize(Request $request, $deviceId)
    {
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

        $fileNames = $request->all();
        $totalSize = 0;
        $numberRequested = 0;
        $numberFound = 0;

        // Add caching to optimize this part for multiple requests
        // https://flysystem.thephpleague.com/docs/advanced/caching/
//        $cacheStore = new MemoryStore();
        $localAdapter = new Local(storage_path() . '/respondent-photos');
//        $adapter = new CachedAdapter($localAdapter, $cacheStore);
        $filesystem = new Filesystem($localAdapter);

        foreach($fileNames as $fileName) {
            $numberRequested++;
            $exists = $filesystem->has($fileName);
            if ($exists) {
                $numberFound++;
                $size = $filesystem->getSize($fileName);
                $totalSize += $size;
            }
        }

        return response()->json([
            'total_size' => $totalSize,
            'photos_requested' => $numberRequested,
            'photos_found' => $numberFound], Response::HTTP_OK);
    }

    public function getImage($deviceId, $fileName)
    {
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

        $adapter = new Local(storage_path() . '/respondent-photos');
        $filesystem = new Filesystem($adapter);
        $exists = $filesystem->has($fileName);

        if (!$exists) {
            return response()->json([], Response::HTTP_NOT_FOUND);
        }

        $image = $filesystem->read($fileName);
        $mimetype = $filesystem->getMimetype($fileName);

        return response()->make($image, Response::HTTP_OK, ['content-type' => $mimetype]);
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
    }
}
