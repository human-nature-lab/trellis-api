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

		// Generate the report csv contents and store is with a unique filename
		$fileName = ExportService::createFormExport($formId);
//		$fileName = ExportService::createFormExportBranching($formId);

		// Return the file id that can be downloaded
		return response()->json([
			'fileUrl' => $fileName
		], Response::HTTP_OK);

	}


    /**
     * Responds with a file encoded as a string in the 'contents' of a JSON response.
     * @param Request $request
     * @param $fileName - The name of the file. Files are stored in storage/app.
     * @return \Symfony\Component\HttpFoundation\Response
     */
	public function downloadFile(Request $request, $fileName){

		// This is a security issue if php reads relative file paths. I'm not sure if it will.
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