<?php namespace App\Http\Controllers;

use App\Jobs\FormExportJob;
use App\Jobs\RespondentExportJob;
use App\Jobs\EdgeExportJob;
use App\Models\Edge;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Validator;
use App\Models\Form;
use App\Models\Study;
use App\Models\Export;
use App\Services\ExportService;

class ExportController extends Controller {

    public function exportEdgesData(Request $request, $studyId){

        $validator = Validator::make(
            ['id' => $studyId],
            ['id' => 'required|string|min:36']
        );

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Form id invalid',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        if(Study::where('id', $studyId)->count() === 0){
            return response()->json([
                'msg' => "Study with id, $studyId doesn't exist"
            ], Response::HTTP_NOT_FOUND);
        }

        // Generate the report csv contents and store is with a unique filename
        $fileId = Uuid::uuid4();
        $exportJob = new EdgeExportJob($studyId, $fileId);
        $this->dispatch($exportJob);
//        ExportService::createEdgesExport($studyId);

        // Return the file id that can be downloaded
        return response()->json([
            'exportId' => $fileId
        ], Response::HTTP_OK);

    }


    public function exportRespondentData(Request $request, $studyId){
        $validator = Validator::make(
            ['id' => $studyId],
            ['id' => 'required|string|min:36']
        );

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Form id invalid',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }


        if(Study::where('id', $studyId)->count() === 0){
            return response()->json([
                'msg' => "Study with id, $studyId doesn't exist"
            ], Response::HTTP_NOT_FOUND);
        }


        // Generate the report csv contents and store is with a unique filename
        $exportId = Uuid::uuid4();
        $exportJob = new RespondentExportJob($studyId, $exportId);
//        ExportService::createRespondentExport($studyId);
        $this->dispatch($exportJob);
        // Return the file id that can be downloaded
        return response()->json([
            'exportId' => $exportId
        ], Response::HTTP_OK);

    }


	public function exportFormData(Request $request, $formId){

        $validator = Validator::make(
            ['id' => $formId],
            ['id' => 'required|string|min:36']
        );

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Form id invalid',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

		// Validate that the form with this id exists
		if(Form::where('id', $formId)->count() === 0){
			return response()->json([
				'msg' => "Form with id, $formId doesn't exist"
			], Response::HTTP_NOT_FOUND);
		}

		// Generate the report csv contents and store is with a unique filename
//		$fileName = ExportService::createFormExport($formId);
        $exportId = Uuid::uuid4();
        $exportJob = new FormExportJob($formId, $exportId);

		$this->dispatch($exportJob);

		// Return the file id that can be downloaded
		return response()->json([
			'exportId' => $exportId
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


	public function getAllSavedExports(Request $request){

	    $exports = Export::where('status', '=', 'saved')
            ->orderBy('updated_at', 'desc')
            ->get();

	    return response()->json([
	        'exports' => $exports
        ], Response::HTTP_OK);

    }


    public function getExportStatus(Request $request, $exportId){

	    // TODO: validate the exportId
        $validator = Validator::make(
            ['id' => $exportId],
            ['id' => 'required|string|min:36']
        );

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Export id is invalid',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

	    $export = Export::find($exportId);

        if($export === null){
            return response()->json([
                'msg' => "Export matching $exportId not found",
                'err' => "Export matching $exportId not found"
            ], Response::HTTP_NOT_FOUND);
        }

	    return response()->json([
	        'status' => $export->status
        ], Response::HTTP_OK);

    }

}