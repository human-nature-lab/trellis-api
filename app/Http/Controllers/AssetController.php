<?php namespace App\Http\Controllers;

use App\Models\Asset;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AssetController extends Controller {

  private $audioMimeTypes = [
    'audio/mpeg',
    'audio/ogg',
    'audio/wav',
    'audio/aac',
  ];
  
  private $imageMimeTypes = [
    'image/jpeg',
    'image/png',
    'image/gif',
    'image/bmp',
    'image/webp',
  ];
  
  private $videoMimeTypes = [
    'video/mp4',
    'video/ogg',
    'video/webm',
    'video/flv',
    'video/x-flv',
  ];
  
  private $textMimeTypes = [
    'text/plain',
    'text/csv',
    'text/html',
    'text/css',
    'text/javascript',
    'text/markdown',
    'text/xml',
    'text/json',
  ];

  private $documentTypes = [
    'application/msword',
    'application/pdf',
    'application/vnd.ms-excel',
    'application/vnd.ms-powerpoint',
    'application/vnd.oasis.opendocument.text',
    'application/vnd.oasis.opendocument.spreadsheet',
    'application/vnd.oasis.opendocument.presentation',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
  ];

  private $binaryTypes = [
    'application/octet-stream',
    'application/zip',
    'application/x-rar-compressed',
    'application/x-tar',
    'application/x-7z-compressed',
    'application/x-bzip',
    'application/x-bzip2',
    'application/x-gzip',
    'application/x-xz',
    'application/x-rar',
    'application/x-tar',
    'application/x-7z-compressed',
    'application/x-bzip',
    'application/x-bzip2',
    'application/x-gzip',
    'application/x-xz',
    'application/x-rar',
  ];

  private $typeMap;
  
  public function __construct() {
    $this->typeMap = [
      'audio' => $this->audioMimeTypes,
      'image' => $this->imageMimeTypes,
      'video' => $this->videoMimeTypes,
      'text' => $this->textMimeTypes,
      'document' => $this->documentTypes,
      'binary' => $this->binaryTypes,
    ];
  }

  private function getAssetType ($mimeType) {
    foreach ($this->typeMap as $type => $mimeTypes) {
      if (in_array($mimeType, $mimeTypes)) {
        return $type;
      }
    }
    return 'unknown';
  }

  public function createAsset (Request $req) {
    if (!$req->hasFile('file')) {
      return response()->json([
        'msg' => 'No file uploaded'
      ], Response::HTTP_BAD_REQUEST);
    }
    $file = $req->file('file');
    $asset = new Asset();
    $asset->id = Uuid::uuid4();
    $asset->file_name = $file->getClientOriginalName();
    $asset->mime_type = $file->getMimeType();
    $asset->size = $file->getSize();
    $asset->type = $this->getAssetType($asset->mime_type);
    $asset->should_sync = false;
    
    // TODO: store the asset file
    $path = storage_path('assets');
    $file->move($path, $asset->id);
    $asset->save();

    return response()->json([
      'msg' => 'Asset uploaded',
      'asset' => $asset
    ], Response::HTTP_OK);
    
  }

  public function updateAsset(Request $req, String $assetId) {
    if (!$req->hasFile('file')) {
      return response()->json([
        'msg' => 'No file uploaded'
      ], Response::HTTP_BAD_REQUEST);
    }
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

    $file = $req->file('file');
    $asset->file_name = $file->getClientOriginalName();
    $asset->mime_type = $file->getMimeType();
    $asset->size = $file->getSize();
    $asset->type = $this->getAssetType($asset->mime_type);
    $path = storage_path('assets');
    $file->move($path, $asset->id);
    $asset->save();

    return response()->json([
      'msg' => 'Asset updated',
      'asset' => $asset
    ], Response::HTTP_OK);
  }

  public function getAsset (String $assetId) {
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

    // TODO: send the asset file to the client
    $assetPath = storage_path('assets/') . $asset->id;
    Log::info("Asset path: $assetPath");
    $mimeType = \GuzzleHttp\Psr7\mimetype_from_filename($asset->file_name);
    return response()->download($assetPath, $asset->file_name, [
      'Content-Type' => $mimeType,
      'Original-Mime-Type' => $asset->mime_type,
    ], 'inline');
  }

  public function deleteAsset(String $assetId) {
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
    $asset->delete();
    return response()->json([
      'msg' => 'Asset deleted'
    ], Response::HTTP_OK);
  }

  public function listAssets() {
    $assets = Asset::all();
    return response()->json([
      'assets' => $assets
    ], Response::HTTP_OK);
  }

}