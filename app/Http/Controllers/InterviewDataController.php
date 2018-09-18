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
use App\Models\Question;
use App\Models\QuestionDatum;
use App\Models\RespondentConditionTag;
use App\Models\SectionConditionTag;
use App\Models\SurveyConditionTag;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Flysystem\Exception;
use Ramsey\Uuid\Uuid;
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


    private function makeTree ($questionData, $data, &$qDIndex, &$dIndex) {
        $tree = [];
        $treeMap = [];
        $added = [];

        $addToBranch = function (&$branch, $id) use (&$treeMap, &$added) {
            if (!isset($branch[$id])) {
                $branch[$id] = [];
            }
            $treeMap[$id] = &$branch[$id];
            $added[$id] = 1;
            Log::debug("Added $id");
        };

        // Add any parentless question datum
        foreach ($questionData as $qd) {
            if (!isset($qd['follow_up_datum_id'])) {
                $addToBranch($tree, $qd['id']);
            } else if (!isset($dIndex[$qd['follow_up_datum_id']])) {
                // Add any question datum that are follow up datum to a question not present in the current request
                $addToBranch($tree, $qd['id']);
            }
        }

        // Add the first level of datum
        foreach ($data as $d) {
            if (isset($treeMap[$d['question_datum_id']])) {
                $addToBranch($treeMap[$d['question_datum_id']], $d['id']);
            } else if (!isset($qDIndex[$d['id']])){
                $addToBranch($tree, '');
                $addToBranch($treeMap[''], $d['id']);
            }
        }

        $hasChanged = true;
        $c = 0;
        while ($hasChanged && $c < 100) {
            $hasChanged = false;
            $c++;
            foreach ($questionData as $qd) {
                if (isset($qd['follow_up_datum_id'])) {
                    if (!isset($added[$qd['follow_up_datum_id']])) {
                        $addToBranch($treeMap[$qd['follow_up_datum_id']], $qd['id']);
                        $hasChanged = true;
                    }
                } else {
                    // We should never get here, but just in case
                    $addToBranch($tree, $qd['id']);
                    $hasChanged = true;
                }
            }
            foreach ($data as $d) {
                if (isset($added[$d['question_datum_id']])) {
                    $addToBranch($treeMap[$d['question_datum_id']], $d['id']);
                    $hasChanged = true;
                }
            }
        }

        return $tree;
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
        $datum = $patch['data']['datum'];
        $questionDatum = $patch['data']['question_datum'];

        DB::beginTransaction();

        try {
            // Remove any removed datum
            $removedDatumIds = array_map(function ($d) {
                return $d['id'];
            }, $datum['removed']);
            $removedQuestionDatumIds = array_map(function ($d) {
                return $d['id'];
            }, $questionDatum['removed']);
            Datum::destroy($removedDatumIds);
            QuestionDatum::destroy($removedQuestionDatumIds);

            // Update modified values
            foreach ($datum['modified'] as $d) {
                Datum::where('id', '=', $d['id'])
                    ->update($d);
            }
            foreach ($questionDatum['modified'] as $d) {
                QuestionDatum::where('id', '=', $d['id'])
                    ->update($d);
            }

            $questionDatumIdSieve = array_reduce($questionDatum['added'], function ($map, $qd) {
                $map[$qd['id']] = true;
                return $map;
            }, []);
            $questionDatumToDatumMap = array_reduce($datum['added'], function ($map, $d) {
                if (!isset($map[$d['question_datum_id']])) {
                    $map[$d['question_datum_id']] = [];
                }
                array_push($map[$d['question_datum_id']], $d);
                return $map;
            }, []);

            $questionIdToQuestionDatumMap = array_reduce($questionDatum['added'], function ($map, $qd){
                $map[$qd['question_id']] = $qd;
                return $map;
            },[]);
            $questionIds = array_keys($questionIdToQuestionDatumMap);

            $sectionQuery = Question::whereIn('id', $questionIds)
                ->select('question.id',
                    DB::raw('(select `form_section`.`sort_order` from `form_section` where `form_section`.`section_id` in 
                    (select `section_question_group`.`section_id` from `section_question_group` where `section_question_group`.`question_group_id` = question.question_group_id)) as sort_order'));

            $questionIdInsertOrderMap = $sectionQuery->get()->reduce(function ($map, $r) {
                $map[$r->id] = $r->sort_order;
                return $map;
            }, []);


            uasort($questionDatum['added'], function ($a, $b) use ($questionIdInsertOrderMap) {
               return $questionIdInsertOrderMap[$a['question_id']] - $questionIdInsertOrderMap[$b['question_id']];
            });

            $dontChangeVals = ['id' => true, 'created_at' => true];
            $firstOrNew = function ($class, $o) use ($dontChangeVals) {
                $m = $class::where([
                    'id' => $o['id']
                ])->withTrashed()->first();
                if (is_null($m)) {
                    $m = new $class();
                    $m->id = $o['id'];
                    $m->created_at = $o['created_at'];
                }
                foreach ($o as $key => $val) {
                    if (!isset($dontChangeVals[$key])) {
                        $m->$key = $val;
                    }
                }
                $m->save();
            };

            foreach ($datum['added'] as $d) {
                if (!isset($questionDatumIdSieve[$d['question_datum_id']])) {
                    $did = $d['id'];
                    $firstOrNew(Datum::class, $d);
                }
            }

            foreach ($questionDatum['added'] as $qd) {
                $qid = $qd['id'];
                $firstOrNew(QuestionDatum::class, $qd);
                if (isset($questionDatumToDatumMap[$qd['id']])) {
                    foreach ($questionDatumToDatumMap[$qd['id']] as $d) {
                        $did = $d['id'];
                        Log::debug("Inserting d $did");
                        $firstOrNew(Datum::class, $d);
                    }
                }
            }

            // Add all of the condition tags last
            self::dataPatch(RespondentConditionTag::class, $patch['condition_tags']['respondent']);
            self::dataPatch(SectionConditionTag::class, $patch['condition_tags']['section']);
            self::dataPatch(SurveyConditionTag::class, $patch['condition_tags']['survey']);

        } catch (QueryException $exception) {
            Log::debug('rolling back transaction');
            Log::debug($exception);
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

        // Get any preload_action rows for the form and respondent that have not already
        // been copied to the action table
        $preloadActions = DB::table('preload_action')
            ->whereRaw("
                respondent_id = (
                  select respondent_id from survey where id = (select survey_id from interview where id = ?)
                ) 
                and question_id in (
                  select id from question where question_group_id in (
                    select question_group_id from section_question_group where section_id in (
                      select section_id from form_section where form_id = (
                        select form_id from survey where id = (select survey_id from interview where id = ?)
                      )
                    )
                  )
                )
                and not exists (
                  select * from action where interview_id in (
                    select id from interview where survey_id = (select survey_id from interview where id = ?)
                  ) and preload_action_id = preload_action.id
                )", [$interviewId, $interviewId, $interviewId])
            ->get();

        foreach ($preloadActions as $preloadAction) {
            $actionId = Uuid::uuid4();
            $actionModel = new Action;
            $actionModel->id = $actionId;
            $actionModel->question_id = $preloadAction->question_id;
            $actionModel->payload = $preloadAction->payload;
            $actionModel->action_type = $preloadAction->action_type;
            $actionModel->interview_id = $interviewId;
            $actionModel->preload_action_id = $preloadAction->id;
            $actionModel->save();
        }

        $actions = Action::whereIn('action.interview_id', function ($q) use ($interview) {
            $q->select('id')->from('interview')->where('survey_id', $interview->survey_id);
        })->get();

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
            ], Response::HTTP_BAD_REQUEST);
        }
        $actions = array_map(function ($action) {
            if (isset($action['payload']) && !is_string($action['payload'])) {
                $action['payload'] = json_encode($action['payload']);
            }
            $fields = ['created_at', 'payload', 'action_type', 'survey_id', 'deleted_at','section_repetition','section_follow_up_repetition','question_id'];
            foreach ($fields as $field) {
                if (!isset($action[$field])) {
                    $action[$field] = null;
                }
            }
            return $action;
        }, $request->get('actions'));

        // Handle this transaction manually and return an error if we fail to insert the data
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