<?php

namespace App\Traits;

use App\Models\Asset;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait AssetDownloader {
  public function downloadAsset (Asset $asset) {
    $adapter = new Local(storage_path() . '/assets');
    $fs = new Filesystem($adapter);

    if (!$fs->has($asset->id)) {
      return response()->json([
        'msg' => 'File not found.'
      ], Response::HTTP_NOT_FOUND);
    }
    return new StreamedResponse(function () use ($fs, $asset) {
      $stream = $fs->readStream($asset->id);
      fpassthru($stream);
    }, Response::HTTP_OK, [
      'Content-Type' => $asset->mime_type,
      'Content-disposition' => 'inline; filename="' . $asset->id . '"',
    ]);
  }
}