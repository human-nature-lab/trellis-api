<?php namespace App\Http\Controllers;

use App\Models\UploadLog;
use Illuminate\Http\Response;
use Validator;

class UploadLogController extends Controller {

    public function getUploadLogs ($uploadId) {
        $validator = Validator::make([
            'uploadId' => $uploadId
        ], [
            'uploadId' => 'required|string|min:36|exists:upload,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors()
            ], $validator->statusCode());
        }

        $logs = UploadLog::where('upload_id', $uploadId)->get();
        return response()->json($logs, Response::HTTP_OK);

    }

}