<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PhotoController extends Controller
{
    public function getPhoto($id)
    {
        $photoModel = Photo::find($id);
        if ($photoModel != null) {
            // TODO: this hard-coded path should be put in the config
            // also, this folder will contain geo photos as well
            $adapter = new Local(storage_path() . '/respondent-photos');
            $filesystem = new Filesystem($adapter);
            $exists = $filesystem->has($photoModel->file_name);
            if ($exists) {
                $contents = $filesystem->read($photoModel->file_name);
                $base64 = base64_encode($contents);
                return response($base64)
                ->header('Content-Type', 'image/jpeg')
                ->header('Pragma', 'public')
                ->header('Content-Disposition', 'inline; filename="'.$photoModel->file_name.'"')
                ->header('Cache-Control', 'max-age=60, must-revalidate');
            }
        }

        return Response::HTTP_NOT_FOUND;
    }


    public function getZipPhotos(Request $request){

        $imageIds = $request->input('ids');
        $fileNames = Photo::whereIn('id', $imageIds)
            ->lists('file_name');

        $response = new StreamedResponse(function() use ($fileNames) {
            $zip = new \Barracuda\ArchiveStream\ZipArchive('photos.zip');
            $errors = [];
            foreach($fileNames as $fileName){
                $path = storage_path("respondent-photos/$fileName");
                if(is_readable($path)) {
                    $zip->add_file_from_path($fileName, $path);
                } else {
                    Log::error("Unabled to access file at $path");
                    array_push($errors, $fileName);
                }
            }

            // Add error file
            if(count($errors) > 0){
                $zip->add_file('errors.txt', "unable to read:\n" . implode("\n", $errors));
            }

            $zip->finish();
        });


        return $response;

    }
}
