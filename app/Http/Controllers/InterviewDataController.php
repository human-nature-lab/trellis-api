<?php
/**
 * Created by IntelliJ IDEA.
 * User: wi27
 * Date: 4/18/2018
 * Time: 1:06 PM
 */

namespace App\Http\Controllers;


use App\Models\Action;
use App\Models\Datum;
use App\Models\Interview;
use App\Models\QuestionDatum;
use App\Models\RespondentConditionTag;
use App\Models\SectionConditionTag;
use App\Models\SurveyConditionTag;
use DB;
use Doctrine\DBAL\Driver\PDOException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use League\Flysystem\Exception;
use Log;
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
            $model = $class::firstOrNew([
                'id' => $newItem['id']
            ]);
            foreach ($newItem as $key => $value) {
                $model->$key = $value;
            }
            $model->deleted_at = null;
            $model->save();
        }

        $idsToRemove = array_map(function ($o) { return $o['id']; }, $delta['removed']);
        $class::destroy($idsToRemove);

        // Conditions don't get modified. They are just created or deleted
        if (isset($delta['modified'])) {
            foreach ($delta['modified'] as $modifiedItem) {
                $modifiedModel = $class::find($modifiedItem['id']);
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
            'interview_id' => 'required|string|min:36|exists:interview,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $patch = $request->all();
        Log::debug(json_encode($patch));
        $datum = $patch['data']['datum'];
        $questionDatum = $patch['data']['questionDatum'];

        DB::beginTransaction();

        try {
            // Simpler stuff first
            self::dataPatch(RespondentConditionTag::class, $patch['conditionTags']['respondent']);
            self::dataPatch(SectionConditionTag::class, $patch['conditionTags']['section']);
            self::dataPatch(SurveyConditionTag::class, $patch['conditionTags']['survey']);

            // Remove any removed datum
            $removedDatumIds = array_map(function ($d) {
                return $d['id'];
            }, $datum['removed']);
            $removedQuestionDatumIds = array_map(function ($d) {
                return $d['id'];
            }, $questionDatum['removed']);
            Datum::destroy($removedDatumIds);
            Questiondatum::destroy($removedQuestionDatumIds);

//            QuestionDatum::insert($questionDatum['added']);
//            Datum::insert($datum['added']);

            // Update modified values
            foreach ($datum['modified'] as $d) {
                Datum::where('id', '=', $d['id'])
                    ->update($d);
            }
            foreach ($questionDatum['modified'] as $d) {
                QuestionDatum::where('id', '=', $d['id'])
                    ->update($d);
            }

            $insertSieve = [];
            $tries = 0;
            $failureCount = 0;
            do {
                foreach ($questionDatum['added'] as $qD) {
                    if (!isset($insertSieve[$qD['id']])) {
                        $insertSieve[$qD['id']] = false;
                    }
                    try {
                        $d = new QuestionDatum($qD);
                        $d->save();
                        $insertSieve[$qD['id']] = true;
                    }
                    catch (PDOException $e) {
                        $failureCount++;
                    }
                    catch (Exception $e) {
                        Log::debug($e);
                        $failureCount++;
                    }
                }
                foreach ($datum['added'] as $d) {
                    if (!isset($insertSieve[$d['id']])) {
                        $insertSieve[$d['id']] = false;
                    }
                    try {
                        $da = new Datum($d);
                        $da->save();
                        $insertSieve[$d['id']] = true;
                    }
                    catch (PDOException $e) {
                        $failureCount++;
                    }
                    catch (Exception $e) {
                        Log::debug($e);
                        $failureCount++;
                    }
                }
            } while ($failureCount > 0 && $tries < 3);

            $insertFailures = 0;
            foreach ($insertSieve as $key => $val) {
                if (!$val) {
                    $insertFailures++;
                }
            }
            if ($insertFailures > 0) {
                throw new Exception('Unable to insert questionDatum and datum');
            }

        } catch (Exception $exception) {
            Log::debug('rolling back transaction');
            DB::rollBack();
            return response()->json([
                'msg' => $exception
            ], Response::HTTP_BAD_REQUEST);
        }

        DB::commit();

        return response()->json([
            'msg' => 'successful patch'
        ], Response::HTTP_CREATED);
    }

    /**
     * Get the questionDatum, datum and conditionTags that have already been created for this survey.
     * @param $interviewId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getInterviewDataByInterviewId ($interviewId) {
        $validator = Validator::make([
            'interview_id' => $interviewId
        ], [
            'interview_id' => 'required|string|min:36|exists:interview,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $interview = Interview::with('surveyData')->find($interviewId);
        return response()->json([
            'data' => $interview->surveyData['data'],
            'conditionTags' => [
                'survey' => $interview->surveyData['surveyConditionTags'],
                'section' => $interview->surveyData['sectionConditionTags'],
                'respondent' => $interview->surveyData['respondentConditionTags'],
            ]
        ], Response::HTTP_OK);

    }

    /**
     * Get all of the actions associated with an interview via the survey
     * @param $interviewId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getInterviewActionsByInterviewId ($interviewId) {
        $validator = Validator::make([
            'interview_id' => $interviewId
        ], [
            'interview_id' => 'required|string|min:36|exists:interview,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $interview = Interview::find($interviewId);

        $actions = Action::where('action.survey_id', '=', $interview->survey_id)
            ->get();

        return response()->json([
            'actions' => $actions
        ], Response::HTTP_OK);

    }

    /**
     * Store the actions for an interview
     * @param Request $request
     * @param $interviewId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function saveInterviewActions (Request $request, $interviewId) {
        $validator = Validator::make([
            'interview_id' => $interviewId
        ], [
            'interview_id' => 'required|string|min:36|exists:interview,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err' => $validator->errors()
            ], $validator->statusCode());
        }
        $actions = array_map(function ($action) {
            if (isset($action['payload']) && !is_string($action['payload'])) {
                $action['payload'] = json_encode($action['payload']);
            }
            $fields = ['created_at', 'payload', 'action_type', 'survey_id', 'deleted_at','section','page','section_repetition','section_follow_up_repetition','question_id'];
            foreach ($fields as $field) {
                if (!isset($action[$field])) {
                    $action[$field] = null;
                }
            }
            return $action;
        }, $request->get('actions'));

        // Handle this tranaction manually and return an error if we fail to insert the data
        DB::beginTransaction();

        try {
            Action::insert($actions);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'msg' => $exception
            ], Response::HTTP_BAD_REQUEST);
        }

        DB::commit();

        return response()->json([
            'msg' => 'ok'
        ], Response::HTTP_OK);
    }
}