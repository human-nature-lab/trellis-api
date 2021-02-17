<?php

namespace App\Http\Controllers;

use App\Jobs\ActionReportJob;
use App\Jobs\CleanReportsJob;
use App\Jobs\EdgeReportJob;
use App\Jobs\FormReportJob;
use App\Jobs\GeoReportJob;
use App\Jobs\InterviewReportJob;
use App\Jobs\RespondentGeoJob;
use App\Jobs\RespondentReportJob;
use App\Jobs\TimingReportJob;
use App\Models\Form;
use App\Models\Report;
use App\Models\Study;
use App\Reports\ReportRunner;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use stdClass;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller {

  public function getAvailableReports() {
    return response()->json(ReportRunner::allReports(), Response::HTTP_OK);
  }

  public function getLatestReports (string $studyId) {
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
    return response()->json(ReportRunner::allReports(), Response::HTTP_OK);
  }

  /**
   * Responds with the latest reports and the files created for those reports.
   * @param Request $request
   * @param $studyId
   * @return \Illuminate\Http\JsonResponse
   */
  public function getLatestStudyReports(Request $request, $studyId) {
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
    $distinctReportTypes = Report::select('type', 'study_id', 'form_id')
      ->where('type', '!=', 'failed')
      ->distinct();
    $distinctReportTypes = $distinctReportTypes->get();
    $distinctSieve = [];

    foreach ($distinctReportTypes as $rType) {
      $distinctSieve[$rType->type . $rType->study_id . $rType->form_id] = false;
    }

    $reports = Report::with('files')
      ->orderBy('created_at', 'desc')
      ->where('type', '!=', 'failed')
      ->limit(200)
      ->get();

    $reports = $reports->filter(function ($report) use (&$distinctSieve) {
      if (!$distinctSieve[$report->type . $report->study_id . $report->form_id]) {
        $distinctSieve[$report->type . $report->study_id . $report->form_id] = true;
        return true;
      }
      return false;
    });

    return response()->json([
      'reports' => $reports->values()
    ], Response::HTTP_OK);
  }


  /**
   * Dispatch multiple reports at the same time. Responds with the report objects for each reporting job submitted.
   * @param Request $request
   * @param $studyId
   * @return \Illuminate\Http\JsonResponse
   * @throws \Exception
   */
  public function dispatchReports(Request $request, $studyId) {

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
  public function downloadReports(Request $request, $studyId) {
    $validator = Validator::make(array_merge($request->all(), [
      'studyId' => $studyId
    ]), [
      'reports' => 'required|array|exists:report,id',
      'studyId' => 'string|min:36|max:41|exists:study,id'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'msg' => $validator->errors()
      ], $validator->statusCode());
    }

    $localeId = "48984fbe-84d4-11e5-ba05-0800279114ca";
    $study = Study::find($studyId);
    $reports = Report::with('files')
      ->whereIn('id', $request->get('reports'))
      ->get();

    return new StreamedResponse(function () use ($reports, $localeId, $study) {
      $zip = new \ZipStream\ZipStream('reports.zip');
      $errors = [];
      foreach ($reports as $report) {
        if ($report->type === 'form') {
          $form = Form::with("nameTranslation")
            ->find($report->form_id);
          foreach ($report->files as $file) {
            $formName = ReportService::translationToText($form->nameTranslation, $localeId);;
            $zipName = $file->file_type . "/" . $formName . '_' . $file->file_type . '_export.csv';
            $path = storage_path("app/" . $file->file_name);
            if (is_readable($path)) {
              $zip->addFileFromPath($zipName, $path);
            } else {
              Log::error("Unable to access file at $path");
              array_push($errors, $path);
            }
          }
        } else {
          foreach ($report->files as $file) {
            $zipName = $study->name . '_' . $report->type . '_export.csv';
            $path = storage_path("app/" . $file->file_name);
            if (is_readable($path)) {
              $zip->addFileFromPath($zipName, $path);
            } else {
              Log::error("Unable to access file at $path");
              array_push($errors, $path);
            }
          }
        }
      }
      // Add error file
      if (count($errors) > 0) {
        $zip->addFile('errors.txt', "unable to read:\n" . implode("\n", $errors));
      }
      $zip->finish();
    });
  }

  /**
   * Get report object(s) by id. Multiple ids should be delimited by commas.
   * @param Request $request
   * @param $studyId
   * @param $reportIdString
   * @return \Illuminate\Http\JsonResponse
   */
  public function getReports(Request $request, $studyId, $reportIdString) {

    $reportIds = explode(',', $reportIdString);

    $validator = Validator::make([
      'studyId' => $studyId,
      'reports' => $reportIds
    ], [
      'studyId' => 'string|min:36|max:41|exists:study,id',
      'reports' => 'array|exists:report,id'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'msg' => $validator->errors()
      ], $validator->statusCode());
    }

    $reports = Report::whereIn('id', $reportIds)->get();

    return response()->json([
      'reports' => $reports
    ], Response::HTTP_OK);
  }


  public function cleanReports(Request $request) {

    $job = new CleanReportsJob(date_default_timezone_get());
    $this->dispatch($job);
  }
}
