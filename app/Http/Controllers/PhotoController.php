<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Response;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

class PhotoController extends Controller
{
	public function getPhoto($id) {

		$photoModel = Photo::find($id);
		if ($photoModel != null) {
			$adapter = new Local('/var/www/trellis-api/storage/respondent-photos');
			$filesystem = new Filesystem($adapter);
			$exists = $filesystem->has($photoModel->file_name);
			if ($exists) {
				$contents = $filesystem->read($photoModel->file_name);
				$base64 = base64_encode($contents);
				return response($base64)
				->header('Content-Type','image/jpeg')
				->header('Pragma','public')
				->header('Content-Disposition','inline; filename="'.$photoModel->file_name.'"')
				->header('Cache-Control','max-age=60, must-revalidate');
			}
		}

		return Response::HTTP_NOT_FOUND;
	}
}
