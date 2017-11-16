<?php namespace App\Http\Controllers;

use App\Jobs\FormReportJob;
use App\Jobs\RespondentReportJob;
use App\Jobs\EdgeReportJob;
use App\Models\Edge;
use App\Models\Locale;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Validator;
use App\Models\Form;
use App\Models\Study;
use Illuminate\Support\Facades\Log;
use App\Models\Report;
use App\Models\ReportFile;
use App\Services\ReportService;

class ReportController extends Controller {

    public function dispatchEdgesReport(Request $request, $studyId){

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
        $reportId = Uuid::uuid4();
        $reportJob = new EdgeReportJob($studyId, $reportId);
//        $this->dispatch($reportJob);
        $reportJob->handle();
//        ReportService::createEdgesExport($studyId);

        // Return the file id that can be downloaded
        return response()->json([
            'reportId' => $reportId
        ], Response::HTTP_OK);

    }


    public function dispatchRespondentReport(Request $request, $studyId){

        // TODO: add configuration options
        $config = new \stdClass();

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
        $reportId = Uuid::uuid4();
        $reportJob = new RespondentReportJob($studyId, $reportId, $config);
//        ReportService::createRespondentExport($studyId);
        $reportJob->handle();
//        $this->dispatch($reportJob);
        // Return the file id that can be downloaded
        return response()->json([
            'reportId' => $reportId
        ], Response::HTTP_OK);

    }


	public function dispatchFormReport(Request $request, $formId){

        // TODO: grab configuration options from the request parameters and validate them
        $config = new \stdClass();
        $config->studyId = $request->input('studyId');
        $config->useChoiceNames = $request->input('shouldUseChoiceNames');
        $config->locale = $request->input('locale');

        $validator = Validator::make(
            [
                'id' => $formId,
                'studyId' => $config->studyId,
                'useChoiceNames' => $config->useChoiceNames,
                'locale' => $config->locale,
            ],
            [
                'id' => 'required|string|min:36',
                'studyId' => 'required|string|min:36',
                'useChoiceNames' => 'boolean',
                'locale' => 'string|min:36',
            ]
        );

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'One or more invalid parameter',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

		// Validate that the form with this id exists
		if(Form::where('id', $formId)->count() === 0){
			return response()->json([
				'msg' => "Form with id, $formId doesn't exist"
			], Response::HTTP_NOT_FOUND);
		}

		// Try the get the user supplied locale
        $localeModel = null;
		if($config->locale) {
            $localeModel = Locale::find($config->locale);
            Log::debug("Provided locale $config->locale doesn't match any locale");
        }

		// Default to the first locale in the study if the user's didn't work
		if(!$localeModel){
		    $localeModel = Study::where('study.id', '=', $config->studyId)
                ->join('locale', 'study.default_locale_id', '=', 'locale.id')
                ->first();
        }

        if(!$localeModel){
            return response()->json([
                'msg' => 'Locale not found'
            ], Response::HTTP_NOT_FOUND);
        }

		// Run the FormReportJob
        $reportId = Uuid::uuid4();
        $reportJob = new FormReportJob($formId, $reportId, $config);
        $reportJob->handle();
//		$this->dispatch($reportJob);

		// Return the file id that can be downloaded
		return response()->json([
			'reportId' => $reportId
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
				'msg' => "Report $fileName doesn't exist"
			], Response::HTTP_NOT_FOUND);
		}

		$headers = [
		    "Content-Type: application/zip",
            "Content-Length: " . filesize($filePath)
        ];

        return response()->download($filePath, $fileName);

		$fileContents = file_get_contents($filePath);
		return response()->json([
			'contents' => $fileContents
		], Response::HTTP_OK);

	}


	public function getAllSavedReports(Request $request){

	    $reports = Report::where('status', '=', 'saved')->with('files')
            ->orderBy('updated_at', 'desc')
            ->get();

//	    foreach($reports as &$report){
//            $report->files = ReportFile::where('report_id', '=', $report->id)
//                ->get();
//        }

	    return response()->json([
	        'reports' => $reports
        ], Response::HTTP_OK);

    }


    public function getReportStatus(Request $request, $reportId){

	    // TODO: validate the exportId
        $validator = Validator::make(
            ['id' => $reportId],
            ['id' => 'required|string|min:36']
        );

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Report id is invalid',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

	    $report = Report::find($reportId);

        if($report === null){
            return response()->json([
                'msg' => "Report matching $reportId not found",
                'err' => "Report matching $reportId not found"
            ], Response::HTTP_NOT_FOUND);
        }

	    return response()->json([
	        'status' => $report->status
        ], Response::HTTP_OK);

    }


    public function getReport(Request $request, $reportId){
        $validator = Validator::make(
            ['id' => $reportId],
            ['id' => 'required|string|min:36']
        );

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Report id is invalid',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $report = Report::with('files')->find($reportId);

        if($report === null){
            return response()->json([
                'msg' => "Report matching $reportId not found",
                'err' => "Report matching $reportId not found"
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json($report, Response::HTTP_OK);

    }

}