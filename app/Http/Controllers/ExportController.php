<?php namespace App\Http\Controllers;

// use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\Form;
use App\Services\ExportService;

class ExportController extends Controller {

	public function exportFormData(Request $request, $formId){

		// Validate that the form exists
		if(Form::where('id', $formId)->count() === 0){
			return response()->json([
				'msg' => "Form with $formId doesn't exist"
			], Response::HTTP_NOT_FOUND);
		}

//        return response()->json([
//            'version' => phpversion()
//        ], Response::HTTP_OK);
		
		// Generate the report csv contents and store is with a unique filename
		$fileName = ExportService::createExport($formId);



		// TODO: return the file id to be downloaded
		return response()->json([
			'fileUrl' => $fileName
		], Response::HTTP_OK);

	}

	public function downloadFile(Request $request, $fileName){

		// This is a security issue if php reads relative file paths
		$filePath = storage_path("app/") . $fileName;

		// Validate that the export exists
		if(!file_exists($filePath)){
			return response()->json([
				'msg' => "Export $fileName doesn't exist"
			], Response::HTTP_NOT_FOUND);
		}

		$fileContents = file_get_contents($filePath);
		return response()->json([
			'contents' => $fileContents
		], Response::HTTP_OK);

	}

}