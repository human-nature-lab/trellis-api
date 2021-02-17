<?php

namespace App\Http\Controllers;

use App\Jobs\ReportJob;
use App\Models\Form;
use App\Models\Report;
use App\Models\Study;
use App\Reports\ReportRunner;
use App\Services\ReportService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportControllerV2 extends Controller {

  function getAvailableReports () {
    $reports = (new Collection(ReportRunner::allReports()))->map(function ($r) {
      unset($r['filename']);
      return $r;
    });
    return response()->json($reports, Response::HTTP_OK);
  }

  function getCompletedReportsPage (Request $request, string $studyId, string $report) {
    $validator = Validator::make([
      "name" => $report,
      "study" => $studyId,
    ], [
      "name" => "string",
      "study" => "string|exists:study,id",
    ]);

    if ($validator->fails()) {
      return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
    }

    return Report::with('files')->
      where("study_id", $studyId)->
      where('name', $report)->
      orderBy('created_at', 'desc')->
      simplePaginate(10);
  }
  
  function getLatestReports (Request $request, string $studyId) {
    $validator = Validator::make([
      'reports' => $request->get('reports'),
      'study' => $studyId,
    ], [
      'study' => 'string|exists:study,id',
      'reports' => 'string',
    ]);
    $reports = trim($request->input('reports'));
    $reports = strlen($reports) > 0 ? explode(',', $request->get('reports')) : [];
    if ($validator->fails() || count($reports) == 0) {
      return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
    }
    $reportPool = Report::where('study_id', $studyId)->
      whereIn('name', $reports)->
      orderBy('created_at', 'desc')->
      select('id', 'name');
    $idMap = [];
    foreach ($reportPool->cursor() as $report) {
      if (!isset($idMap[$report->name])) {
        $idMap[$report->name] = $report->id;
      }
      if (count($idMap) == count($reports)) {
        break;
      }
    }
    $ids = array_values($idMap);
    Log::info($ids);
    return Report::whereIn('id', $ids)->get();
  }


  function get (string $studyId, string $reportId) {
    return Report::with('files')->find($reportId);
  }
  
  
  function runReportJob (Request $request, string $studyId, string $report) {
    $validator = Validator::make([
      'study' => $studyId,
      'config' => $request->input('config')
    ], [
      'study' => 'string|exists:study,id',
      'config' => 'string'
    ]);
    if ($validator->fails()) {
      return response()->json([
        'errors' => $validator->errors()
      ], Response::HTTP_BAD_REQUEST);
    }
    $config = json_decode($request->input('config'), false);
    if (is_null($config)) {
      return response()->json([
        'errors' => 'Config must be set'
      ], Response::HTTP_BAD_REQUEST);
    }
    $job = new ReportJob($report, $studyId, (array)$config);
    $this->dispatch($job);
    return response()->json([
      'message' => 'dispatched'
    ], Response::HTTP_ACCEPTED);
  }

  // Stream several reports existing report files
  function downloadReports (Request $request, string $studyId) {
    $validator = Validator::make(array_merge($request->all(), [
      'studyId' => $studyId
    ]), [
      'reports' => 'required|array|exists:report,id',
      'studyId' => 'string|min:36|max:41|exists:study,id'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'msg' => $validator->errors()
      ], Response::HTTP_BAD_REQUEST);
    }
    $reports = Report::with('files')
      ->whereIn('id', $request->get('reports'))
      ->get();

    return new StreamedResponse(function () use ($reports) {
      $zip = new \ZipStream\ZipStream('trellis-reports.zip');
      $errors = [];
      foreach ($reports as $report) {
        $zip->addFile("config.json", $report->config);
        foreach ($report->files as $file) {
          $ext = $file->file_type;
          $type = $file->data_type;
          $zipName = "$report->name/$type.$ext";
          $path = storage_path("app/" . $file->file_name);
          if (is_readable($path)) {
            $zip->addFileFromPath($zipName, $path);
          } else {
            Log::error("Unable to access file at $path");
            array_push($errors, $path);
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
}