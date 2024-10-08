<?php

namespace app\Http\Controllers;

use App\Models\Asset;
use App\Models\ClientLog;
use App\Models\Photo;
use App\Models\Snapshot;
use App\Models\Device;

use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

use App\Models\Upload;
use App\Traits\AssetDownloader;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use Emgag\Flysystem\Hash\HashPlugin;
use Symfony\Component\Finder\Finder;

use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SyncControllerV2 extends Controller {

  use AssetDownloader;

  public function heartbeat() {
    return response()->json([], Response::HTTP_OK);
  }

  public function authenticate(Request $request, $deviceId) {
    $device = Device::where('device_id', $deviceId)->get();

    if (count($device) === 0) {
      return response()->json([
        'msg' => 'URL resource not found'
      ], Response::HTTP_UNAUTHORIZED);
    }

    return response()->json([], Response::HTTP_OK);
  }

  public function getSnapshotFileSize(Request $request, $deviceId, $snapshotId) {
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


  public function downloadSnapshot(Request $request, $deviceId, $snapshotId) {
    return $this->doSnapshotDownload($request, $snapshotId);
  }

  private function doSnapshotDownload(Request $request, $snapshotId) {
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

    $file = storage_path() . '/snapshot/' . $snapshot->file_name;
    $headers = array(
      'Content-Type: application/zip',
      'Content-Length: ' . filesize($file)
    );
    return response()->download($file, $snapshot->file_name, $headers);
  }

  public function listUploads(Request $request) {
    $validator = Validator::make($request->all(), [
      'limit' => 'nullable|number',
      'page' => 'nullable|integer',
      'orderBy' => 'nullable|string',
      'direction' => 'nullable|boolean'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'msg' => $validator->errors()
      ], $validator->statusCode());
    }

    $limit = $request->get('limit', 500);
    $page = $request->get('page', 0);
    $orderBy = $request->get('orderBy', 'created_at');
    $direction = $request->get('direction', 1);
    // This method doesn't work if the case of the device_id varies between tables
    // $uploads = Upload::with('device')->get();
    $uploadQuery = DB::table('upload')
      ->select('upload.*', DB::raw('(select name from device d where d.device_id like upload.device_id and d.deleted_at is null limit 1) as device_name'))
      ->orderBy($orderBy, $direction ? 'desc' : 'asc')
      ->whereNull('upload.deleted_at')
      ->take($limit)
      ->skip($page * $limit);

    // $currentQuery = $uploadQuery->toSql();
    // Log::info('$currentQuery: ' . $currentQuery);

    $uploads = $uploadQuery->get();

    return response()->json(
      ['uploads' => $uploads],
      Response::HTTP_OK
    );
  }

  public function listSnapshots() {
    $snapshots = Snapshot::all();

    return response()->json(
      ['snapshots' => $snapshots],
      Response::HTTP_OK
    );
  }

  public function generateSnapshot(Request $request) {
    set_time_limit(0);
    $args = '--quick-check --no-inaccessible-data --vacuum';
    $force = $request->get('force', false);
    if ($force) {
      Log::info('forcing snapshot');
      $args .= ' --force';
    }

    $exitCode = Artisan::call('trellis:study:snapshot ' . $args);

    if ($exitCode == 1) {
      return response()->json(
        ['translation' => 'snapshot_failure'],
        Response::HTTP_INTERNAL_SERVER_ERROR
      );
    } else if ($exitCode == 2) {
      return response()->json([
        'translation' => 'snapshot_running'
      ], Response::HTTP_LOCKED);
    } else if ($exitCode === 3) {
      return response()->json([
        'translation' => 'snapshot_up_to_date'
      ], Response::HTTP_OK);
    } else {
      return response()->json(
        [],
        Response::HTTP_OK
      );
    }
  }

  public function processUploads(Request $request) {
    $validator = Validator::make($request->all(), [
      'uploads' => 'array|exists:upload,id',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'msg' => $validator->errors(),
      ], Response::HTTP_BAD_REQUEST);
    }

    set_time_limit(0);

    $uploads = $request->input('uploads', []);
    $ids = implode(' ', $uploads);
    $res = Artisan::call("trellis:import:upload $ids");

    Log::info(json_encode($res));

    if ($res === 0) {
      return response()->json(
        ['translation' => 'uploads_processed'],
        Response::HTTP_OK
      );
    } else if ($res == 2) {
      return response()->json([
        'translation' => 'uploads_running'
      ], Response::HTTP_LOCKED);
    } else {
      return response()->json([
        'translation' => 'uploads_process_failure'
      ], Response::HTTP_BAD_REQUEST);
    }
  }

  public function verifyUpload(Request $request, $deviceId) {
    $adapter = new Local(storage_path() . '/uploads-pending');
    $filesystem = new Filesystem($adapter);
    $filesystem->addPlugin(new HashPlugin);
    $exists = $filesystem->has($request->get('fileName'));

    if (!$exists) {
      return response()->json([
        'msg' => 'Upload file not found.'
      ], Response::HTTP_BAD_REQUEST);
    }

    $md5 = $filesystem->hash($request->get('fileName'), 'md5');

    if ($md5 <> $request->get('md5hash')) {
      return response()->json([
        'msg' => 'Calculated hash does not match provided hash.'
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
    if (!$request->hasFile('file')) {
      return response()->json([
        'msg' => 'File not present in request.',
      ], Response::HTTP_BAD_REQUEST);
    }

    $file = $request->file('file');
    $fileName = $request->get('fileName');
    $uploadPath = storage_path() . '/uploads-pending';

    if (!$request->file('file')->isValid()) {
      return response()->json([
        'msg' => 'Upload failed.',
      ], Response::HTTP_BAD_REQUEST);
    }

    $file->move($uploadPath, $fileName);

    return response()->json([], Response::HTTP_OK);
  }

  public function uploadImage(Request $request, $deviceId) {
    if (!$request->hasFile('file')) {
      return response()->json([
        'msg' => 'File not present in request.',
      ], Response::HTTP_BAD_REQUEST);
    }

    $file = $request->file('file');
    $fileName = $request->get('fileName');
    $uploadPath = storage_path() . '/respondent-photos';

    if (!$request->file('file')->isValid()) {
      return response()->json([
        'msg' => 'Upload failed.',
      ], Response::HTTP_BAD_REQUEST);
    }

    $file->move($uploadPath, $fileName);

    return response()->json([], Response::HTTP_OK);
  }

  public function uploadAsset(Request $request, String $deviceId, String $assetId) {
    if (!$request->hasFile('file')) {
      return response()->json([
        'msg' => 'File not present in request.',
      ], Response::HTTP_BAD_REQUEST);
    }

    $file = $request->file('file');
    $fileName = $request->get('fileName');
    $uploadPath = storage_path() . '/assets';

    if (!$request->file('file')->isValid()) {
      return response()->json([
        'msg' => 'Upload failed.',
      ], Response::HTTP_BAD_REQUEST);
    }

    $asset = Asset::find($assetId);
    if ($asset) {
      $md5 = md5_file($file->getRealPath());
      if ($md5 !== $asset->md5_hash) {
        return response()->json([
          'msg' => 'MD5 hash does not match.'
        ], Response::HTTP_BAD_REQUEST);
      }
    }

    $file->move($uploadPath, $assetId);

    return response()->json([], Response::HTTP_OK);
  }

  public function getLatestSnapshot() {
    $latestSnapshot = Snapshot::where('deleted_at', null)
      ->orderBy('created_at', 'desc')
      ->first();

    return response()->json($latestSnapshot, Response::HTTP_OK);
  }

  public function tokenSnapshotDownload(Request $request, $snapshotId) {
    return $this->doSnapshotDownload($request, $snapshotId);
  }

  public function getSnapshotInfo(Request $request, $deviceId) {
    $latestSnapshot = Snapshot::where('deleted_at', null)
      ->orderBy('created_at', 'desc')
      ->first();

    return response()->json($latestSnapshot, Response::HTTP_OK);
  }

  public function getPendingUploads(Request $request, $deviceId) {
    /* Returns both pending and error uploads to prevent end-users from downloading before their upload has been processed */
    $pendingUploads = Upload::where('deleted_at', null)
      ->where('status', 'PENDING')
      ->orWhere('status', 'FAILED')
      ->get();

    return response()->json($pendingUploads, Response::HTTP_OK);
  }

  public function getImageSize(Request $request) {
    $fileNames = $request->all();

    $localAdapter = new Local(storage_path() . '/respondent-photos');
    $filesystem = new Filesystem($localAdapter);

    $sampleCount = 0;
    $numberFound = 0;
    $totalNumber = count($fileNames);
    $sampleSize = 0;
    $useEstimate = false;
    foreach ($fileNames as $fileName) {
      $sampleCount++;
      try {
        $sampleSize += $filesystem->getSize($fileName);
        $numberFound++;
      } catch (\Exception $e) {
      }

      if ($sampleCount > 1000) {
        $useEstimate = true;
        break;
      }
    }

    if ($useEstimate) {
      $sampleSize = ($sampleSize / $sampleCount) * $totalNumber;
    }

    return response()->json([
      'total_size' => $sampleSize,
      'photos_requested' => $totalNumber,
      'is_estimate' => $useEstimate,
      'photos_found' => $useEstimate ? round(($numberFound / $sampleCount) * $totalNumber) : $numberFound
    ], Response::HTTP_OK);
  }


  public function getImage($deviceId, $fileName) {
    $adapter = new Local(storage_path() . '/respondent-photos');
    $fs = new Filesystem($adapter);

    if (!$fs->has($fileName)) {
      return response()->json([
        'msg' => 'File not found.'
      ], Response::HTTP_NOT_FOUND);
    }
    return new StreamedResponse(function () use ($fs, $fileName) {
      $stream = $fs->readStream($fileName);
      fpassthru($stream);
    }, Response::HTTP_OK, [
      'Content-Type' => 'image/jpeg',
      'Content-disposition' => 'attachment; filename="' . $fileName . '"',
    ]);
  }

  public function getAsset (String $deviceId, String $assetId) {
    $validator = Validator::make([
      'asset_id' => $assetId
    ], [
      'asset_id' => 'required|string|min:32'
    ]);
    if ($validator->fails()) {
      return response()->json([
        'msg' => "Invalid asset id",
        'err' => $validator->errors()
      ], $validator->statusCode());
    }
    $asset = Asset::find($assetId);
    if (!$asset) {
      return response()->json([
        'msg' => "Asset not found"
      ], Response::HTTP_NOT_FOUND);
    }

    return $this->downloadAsset($asset);
  }

  public function listMissingAssets($deviceId) {
    if (ob_get_length()) {
      ob_end_clean(); // disable Lumen's output buffering in order to allow infinite response length without using up memory
    }

    http_response_code(Response::HTTP_OK);

    // $response = app()->handle(Request::create(app('request')->getRequestURI(), app('request')->getMethod()));   // get original response headers for cookies, CORS, etc

    // foreach (explode("\r\n", $response->headers) as $header) {
    //   header($header);
    // }

    header('Content-Type: ' . response()->json()->headers->get('content-type'));    // override content type to ensure that it's application/json

    echo '[';

    $first = true;
    Asset::chunk(1000, function ($assets) use (&$first) {
        foreach ($assets as $asset) {
          $fileName = storage_path() . '/assets/' . $asset->id;
          if (!file_exists($fileName)) {
            if ($first) {
              $first = false;
            } else {
              echo ',';
            }
            echo '"' . $asset->id . '"';
          }
        }
      });
    echo ']';
  }

  public function listMissingImages (String $deviceId) {
    if (ob_get_length()) {
      ob_end_clean(); // disable Lumen's output buffering in order to allow infinite response length without using up memory
    }

    http_response_code(Response::HTTP_OK);

    // $response = app()->handle(Request::create(app('request')->getRequestURI(), app('request')->getMethod()));   // get original response headers for cookies, CORS, etc

    // foreach (explode("\r\n", $response->headers) as $header) {
    //   header($header);
    // }

    header('Content-Type: ' . response()->json()->headers->get('content-type'));    // override content type to ensure that it's application/json

    echo '[';

    $first = true;
    Photo::whereNull('deleted_at')
      ->chunk(1000, function ($photos) use (&$first) {
        foreach ($photos as $photo) {
          $fileName = storage_path() . '/respondent-photos/' . $photo->file_name;
          if (!file_exists($fileName)) {
            if ($first) {
              $first = false;
            } else {
              echo ',';
            }
            echo '"' . $photo->file_name . '"';
          }
        }
      });
    echo ']';
  }

  public function listImages($deviceId) {
    //the fields are fileName:<string>, deviceId:<string>, action:<string>, length:<number>,base64:<string/base64>. Note that base64 uses no linefeeds

    ob_end_clean(); // disable Lumen's output buffering in order to allow infinite response length without using up memory

    http_response_code(Response::HTTP_OK);

    // $response = app()->handle(Request::create(app('request')->getRequestURI(), app('request')->getMethod()));   // get original response headers for cookies, CORS, etc

    // foreach (explode("\r\n", $response->headers) as $header) {
    //   header($header);
    // }

    header('Content-Type: ' . response()->json()->headers->get('content-type'));    // override content type to ensure that it's application/json

    echo '[';

    $path = storage_path() . '/respondent-photos';
    $extensions = ['jpg']; //['jpg', 'gif', 'png'];
    $pattern = '/\.(' . implode('|', array_map('preg_quote', $extensions, array_fill(0, count($extensions), '/'))) . ')$/';
    $first = true;

    foreach ((new Finder())->name($pattern)->files()->in($path) as $file) {
      if ($first) {
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

  public function uploadLogs(Request $request, $deviceId) {
    $logs = $request->get('logs');
    foreach ($logs as $log) {
      $log['created_at'] = Carbon::parse($log['created_at']);
      $log['updated_at'] = Carbon::now();
      ClientLog::firstOrCreate([
        'id' => $log['id']
      ], $log);
    }
    return response()->json([
      'count' => count($logs)
    ], Response::HTTP_OK);
  }
}
