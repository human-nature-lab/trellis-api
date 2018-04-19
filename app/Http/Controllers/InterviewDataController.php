<?php
/**
 * Created by IntelliJ IDEA.
 * User: wi27
 * Date: 4/18/2018
 * Time: 1:06 PM
 */

namespace App\Http\Controllers;


use App\Models\Datum;
use App\Models\Interview;
use App\Models\QuestionDatum;
use App\Models\RespondentConditionTag;
use App\Models\SectionConditionTag;
use App\Models\SurveyConditionTag;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class InterviewDataController
{
    /**
     * Run a single version of the patch on the passed in model class. The patch algorithm assumes that the ids are all
     * that needs to be used to uniquely identify a item from that class.
     *
     * This function assumes that the delta follows this format:
     * [
     *  'added' => [...],
     *  'removed' => [...],
     *  'modified' => [...]
     * ]
     * The modified array is optional, but the others are required
     * @param $class
     * @param $delta
     */
    private function dataPatch ($class, $delta) {
        foreach ($delta['added'] as $newItem) {
            $questionDatum = $class::firstOrNew([
                'id' => $newItem
            ]);
            foreach ($newItem as $key => $value) {
                $questionDatum->$key = $value;
            }
            $questionDatum->deleted_at = null;
            $questionDatum->save();
        }

        $idsToRemove = array_map(function ($o) { return $o->id; }, $delta->removed);
        $class::destroy($idsToRemove);

        // Conditions don't get modified. They are just created or deleted
        if (isset($delta['modified'])) {
            foreach ($delta['modified'] as $modifiedItem) {
                $modifiedModel = $class::find($modifiedItem->id);
                foreach ($modifiedItem as $key => $value) {
                    $modifiedModel->$key = $value;
                }
                $modifiedModel->save();
            }
        }
    }

    /**
     * Take a delta encoding of the interview data and condition tags and modified the state of the database so
     * that it represents the current state of the survey
     * @param Request $request
     * @param $interviewId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateInterviewData (Request $request, $interviewId) {
        $validator = Validator::make([
            'interview_id' => $interviewId
        ], [
            'interview_id' => 'required|string|min:36|exists:interview, id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $patch = $request->input->all();

        // They should all succeed or all fail as one
        DB::transaction(function () use ($patch) {
           self::dataPatch(QuestionDatum::class, $patch['data']['questionDatum']);
           self::dataPatch(Datum::class, $patch['data']['datum']);
           self::dataPatch(RespondentConditionTag::class, $patch['conditionTags']['respondent']);
           self::dataPatch(SectionConditionTag::class, $patch['conditionTags']['section']);
           self::dataPatch(SurveyConditionTag::class, $patch['conditionTags']['form']);
        });

        return response()->json([
            'msg' => 'successful patch'
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Get the questionDatum, datum and conditionTags that have already been created for this survey.
     * @param $interviewId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getInterviewWithData ($interviewId) {
        $validator = Validator::make([
            'interview_id' => $interviewId
        ], [
            'inteview_id' => 'required|string|min:36|exists:interview,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $interview = Interview::find($interviewId)->with('surveyData');
        return response()->json([
            'interview' => $interview
        ], Response::HTTP_OK);

    }
}