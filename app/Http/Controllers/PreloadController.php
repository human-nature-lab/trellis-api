<?php

namespace App\Http\Controllers;

use App\Models\Preload;
use App\Models\Survey;
use App\Services\PreloadActionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class PreloadController extends Controller {

  /**
   * Get preload data for an interview
   * @param $interviewId
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function getPreloadDataByInterviewId($interviewId) {
    $validator = Validator::make([
      'interview_id' => $interviewId
    ], [
      'interview_id' => 'required|exists:interview,id'
    ]);
    if ($validator->fails()) {
      return response()->json([
        'msg' => $validator->errors()
      ], $validator->statusCode());
    }

    $survey = Survey::whereIn('id', function ($q) use ($interviewId) {
      $q->select('survey_id')
        ->from('interview')
        ->where('id', '=', $interviewId);
    })->first();

    $preload = Preload::with('data')
      ->where('respondent_id', '=', $survey->respondent_id)
      ->where('form_id', '=', $survey->form_id);

    return response()->json([
      'preload' => $preload->get()
    ], Response::HTTP_OK);
  }

  public function uploadPreloadActions (Request $request, string $studyId) {
    if (!$request->hasfile('file')) {
      return response()->json([
        'err' => 'must upload CSV file'
      ], [Response::HTTP_BAD_REQUEST]);
    }
    $validator = Validator::Make([
      'studyId' => $studyId
    ], [
      'studyId' => 'required|exists:study,id'
    ]);
    if ($validator->fails()) {
      return response()->json([
        'msg' => $validator->errors()
      ], $validator->statusCode());
    }

    // Loop through all of our rows
    $file = $request->file('file');
    try {
      $actions = PreloadActionService::importPreloadData($studyId, $file->getRealPath());
      return response()->json($actions);
    } catch (\Exception $err) {
      return response()->json([
        'msg' => $err->getMessage(),
      ], Response::HTTP_BAD_REQUEST);
    }   
  }
  
}
