<?php namespace App\Http\Controllers;

use App\Models\Preload;
use App\Models\Survey;
use Illuminate\Http\Response;
use Log;
use Validator;

class PreloadController extends Controller {

    /**
     * Get preload data for an interview
     * @param $interviewId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getPreloadDataByInterviewId ($interviewId) {
        return response()->json([
            'preload' => []
        ], Response::HTTP_OK);

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

        Log::debug($preload->toSql());

        return response()->json([
            'preload' => $preload->get()
        ], Response::HTTP_OK);

    }

}