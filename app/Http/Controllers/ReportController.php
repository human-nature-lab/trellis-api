<?php namespace App\Http\Controllers;

use App\Jobs\ActionReportJob;
use App\Jobs\CleanReportsJob;
use App\Jobs\FormReportJob;
use App\Jobs\InterviewReportJob;
use App\Jobs\RespondentGeoJob;
use App\Jobs\RespondentReportJob;
use App\Jobs\TimingReportJob;
use App\Jobs\EdgeReportJob;
use App\Jobs\GeoReportJob;
use App\Models\Action;
use App\Models\Edge;
use App\Models\Locale;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;
use stdClass;
use Symfony\Component\EventDispatcher\Tests\CallableClass;
use Symfony\Component\HttpFoundation\StreamedResponse;
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

    /**
     * Responds with the latest reports and the files created for those reports.
     * @param Request $request
     * @param $studyId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLatestStudyReports (Request $request, $studyId) {
        $validator = Validator::make([
            'studyId' => $studyId
        ], [
            'studyId' => 'string|min:36|max:41|exists:study,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors()
            ], $validator->statusCode());
        }

        // Get all distinct combinations of type/report_id in the report table. This is necessary because most of the forms
        $distinctReportTypes = Report::distinct('type', 'study_id', 'form_id')->get();
        $distinctSieve = [];

        foreach ($distinctReportTypes as $rType) {
            $distinctSieve[$rType->type . $rType->study_id . $rType->form_id] = false;
        }

        $reports = Report::with('files')->limit(200)->get();

        $reports = $reports->filter(function ($report) use ($distinctSieve) {
            if (!$distinctSieve[$report->type . $report->study_id . $report->form_id]) {
                $distinctSieve[$report->type . $report->study_id . $report->form_id] = true;
                return true;
            }
            return false;
        });

        return response()->json([
            'reports' => $reports
        ], Response::HTTP_OK);
    }


    /**
     * Dispatch multiple reports at the same time. Responds with the report objects for each reporting job submitted.
     * @param Request $request
     * @param $studyId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function dispatchReports (Request $request, $studyId) {

        $studyReportTypes = [
            'respondent' => RespondentReportJob::class,
            'geo' => GeoReportJob::class,
            'action' => ActionReportJob::class,
            'edge' => EdgeReportJob::class,
            'interview' => InterviewReportJob::class,
            'respondent_geo' => RespondentGeoJob::class,
            'timing' => TimingReportJob::class
        ];

        $validStudyTypes = implode(',', array_keys($studyReportTypes));

        $validator = Validator::make($request->all(), [
            'study_types' => 'array|in:' . $validStudyTypes,
            'forms' => 'array|exists:form,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors()
            ], $validator->statusCode());
        }

        $config = $request->get('config') ?: new stdClass();
        $types = $request->get('study_types') ?: [];
        $formIds = $request->get('forms') ?: [];

        $reports = new Collection();

        // Dispatch study reports that are valid
        foreach ($types as $type) {
            $class = $studyReportTypes[$type];
            if (isset($class)) {
                $reportJob = new $class($studyId, $config);
                $this->dispatch($reportJob);
                $reports->push($reportJob->report);
            }
        }
        // Dispatch valid form reports
        foreach ($formIds as $formId) {
            $config->form_id = $formId;
            $reportJob = new FormReportJob($studyId, $formId, $config);
            $this->dispatch($reportJob);
            $reports->push($reportJob->report);
        }

        return response()->json([
            'reports' => $reports
        ], Response::HTTP_OK);
    }


    /**
     * Responds with a compressed file with all of the reports inside
     * @param Request $request
     * @param $studyId
     * @return \Illuminate\Http\JsonResponse|StreamedResponse
     */
    public function downloadReports (Request $request, $studyId) {
        $validator = Validator::make(array_merge($request->all(), [
            'studyId' => $studyId
        ]), [
            'reports' => 'array|exists:report,id',
            'studyId' => 'string|min:36|max:41|exists:study,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors()
            ], $validator->statusCode());
        }

        $localeId = "48984fbe-84d4-11e5-ba05-0800279114ca";
        $study = Study::find($studyId);
        $reports = Report::with('files')->whereIn('id', $request->get('reports'))->get();

        $response = new StreamedResponse(function() use ($reports, $localeId, $study) {
            $zip = new \Barracuda\ArchiveStream\ZipArchive('reports.zip');
            $errors = [];
            foreach($reports as $report){
                if ($report->type === 'form') {
                    $form = Form::with("nameTranslation")
                        ->find($report->report_id);
                    foreach($report->files as $file){
                        $formName = ReportService::translationToText($form->nameTranslation, $localeId);;
                        $zipName = $file->file_type."/".$formName.'_'.$file->file_type.'_export.csv';
                        $path = storage_path("app/".$file->file_name);
                        if (is_readable($path)) {
                            $zip->add_file_from_path($zipName, $path);
                        } else {
                            Log::error("Unable to access file at $path");
                            array_push($errors, $path);
                        }
                    }
                } else {
                    foreach($report->files as $file) {
                        $zipName = $study->name . '_' . $report->type . '_export.csv';
                        $path = storage_path("app/".$file->file_name);
                        if (is_readable($path)) {
                            $zip->add_file_from_path($zipName, $path);
                        } else {
                            Log::error("Unable to access file at $path");
                            array_push($errors, $path);
                        }
                    }
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

    public function dispatchActionsReport(Request $request, $studyId){

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
        $reportJob = new ActionReportJob($studyId, $reportId, $config);

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