<?php namespace App\Http\Controllers;

use App\Jobs\CleanReportsJob;
use App\Jobs\FormReportJob;
use App\Jobs\InterviewReportJob;
use App\Jobs\RespondentReportJob;
use App\Jobs\TimingReportJob;
use App\Jobs\EdgeReportJob;
use App\Jobs\GeoReportJob;
use App\Models\Locale;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\Tests\CallableClass;
use Validator;
use App\Models\Form;
use App\Models\Study;
use Illuminate\Support\Facades\Log;
use App\Models\Report;

class ReportController extends Controller {

    public function dispatchInterviewReport(Request $request, $studyId){

        $config = new \stdClass();

        $validator = Validator::make(
            ['studyId' => $studyId],
            ['studyId' => 'required|string|min:36|exists:study,id']
        );

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Study id invalid',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        // Generate the report csv contents and store is with a unique filename
        $reportId = Uuid::uuid4();
        $reportJob = new InterviewReportJob($studyId, $reportId, $config);

        $this->dispatch($reportJob);

        // Return the file id that can be downloaded
        return response()->json([
            'reportId' => $reportId
        ], Response::HTTP_OK);

    }

    public function dispatchEdgesReport(Request $request, $studyId){

        $config = new \stdClass();

        $validator = Validator::make(
            ['studyId' => $studyId],
            ['studyId' => 'required|string|min:36|exists:study,id']
        );

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Study id invalid',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        // Generate the report csv contents and store is with a unique filename
        $reportId = Uuid::uuid4();
        $reportJob = new EdgeReportJob($studyId, $reportId, $config);

        $this->dispatch($reportJob);

        // Return the file id that can be downloaded
        return response()->json([
            'reportId' => $reportId
        ], Response::HTTP_OK);

    }

    public function dispatchGeoReport(Request $request, $studyId){

        $config = new \stdClass();

        $validator = Validator::make(
            ['studyId' => $studyId],
            ['studyId' => 'required|string|min:36|exists:study,id']
        );

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Study id invalid',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }


        // Generate the report csv contents and store is with a unique filename
        $reportId = Uuid::uuid4();
        $reportJob = new GeoReportJob($studyId, $reportId, $config);

        $this->dispatch($reportJob);

        // Return the file id that can be downloaded
        return response()->json([
            'reportId' => $reportId
        ], Response::HTTP_OK);

    }


    public function dispatchTimingreport(Request $request, $studyId){

        $config = new \stdClass();

        $validator = Validator::make(
            ['studyId' => $studyId],
            ['studyId' => 'required|string|min:36|exists:study,id']
        );

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Study id invalid',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }


        // Generate the report csv contents and store is with a unique filename
        $reportId = Uuid::uuid4();
        $reportJob = new TimingReportJob($studyId, $reportId, $config);

        $this->dispatch($reportJob);

        // Return the file id that can be downloaded
        return response()->json([
            'reportId' => $reportId
        ], Response::HTTP_OK);

    }


    public function dispatchRespondentReport(Request $request, $studyId){

        $config = new \stdClass();

        $validator = Validator::make(
            ['studyId' => $studyId],
            ['studyId' => 'required|string|min:36|exists:study,id']
        );

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Study id invalid',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }


           // Generate the report csv contents and store is with a unique filename
        $reportId = Uuid::uuid4();
        $reportJob = new RespondentReportJob($studyId, $reportId, $config);

        $this->dispatch($reportJob);

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
                'formId' => $formId,
                'studyId' => $config->studyId,
                'useChoiceNames' => $config->useChoiceNames,
                'locale' => $config->locale,
            ],
            [
                'formId' => 'required|string|min:36|exists:form,id',
                'studyId' => 'required|string|min:36',
                'useChoiceNames' => 'nullable|boolean',
                'locale' => 'nullable|string|min:36',
            ]
        );

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'One or more invalid parameter',
                'err' => $validator->errors()
            ], $validator->statusCode());
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
//        $reportJob->handle();
		$this->dispatch($reportJob);

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

	    return response()->json([
	        'reports' => $reports
        ], Response::HTTP_OK);

    }


    public function getReportStatus(Request $request, $reportId){

	    // TODO: validate the exportId
        $validator = Validator::make(
            ['id' => $reportId],
            ['id' => 'required|string|min:36|exists:report,id']
        );

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Report id is invalid',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

	    $report = Report::find($reportId);

	    return response()->json([
	        'status' => $report->status
        ], Response::HTTP_OK);

    }


    public function getReport(Request $request, $reportId){
        $validator = Validator::make(
            ['id' => $reportId],
            ['id' => 'required|string|min:36|exists:report,id']
        );

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Report id is invalid',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $report = Report::with('files')->find($reportId);

        return response()->json($report, Response::HTTP_OK);

    }


    public function cleanReports(Request $request){

        $job = new CleanReportsJob(date_default_timezone_get());
        $this->dispatch($job);

    }

}