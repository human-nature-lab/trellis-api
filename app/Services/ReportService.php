<?php
namespace App\Services;

use Log;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\DB;
use App\Services\FileService;
use App\Classes\Memoization;

class ReportService
{

    public static function createEdgesExport($studyId, $fileId){

        $edges = DB::table('edge')
            ->join('respondent as sourceR', 'sourceR.id', '=', 'edge.source_respondent_id')
            ->join('respondent as targetR', 'targetR.id', '=', 'edge.target_respondent_id')
            ->join('edge_datum', 'edge_datum.edge_id', '=', 'edge.id')
            ->join('datum', 'datum.id', '=', 'edge_datum.datum_id')
            ->join('survey', 'survey.id', '=', 'datum.survey_id')
            ->join('geo as sGeo', 'sGeo.id', '=', 'sourceR.geo_id')
            ->join('geo as tGeo', 'tGeo.id', '=', 'targetR.geo_id')
            ->join('question', 'question.id', '=', 'datum.question_id')
            ->where('survey.study_id', '=', $studyId)
            ->select(
                'sourceR.id as sId',
                'targetR.id as rId',
                'question.var_name',
                'sGeo.latitude as sLat',
                'sGeo.longitude as sLong',
                'sGeo.altitude as sAlt',
                'tGeo.latitude as tLat',
                'tGeo.longitude as tLong',
                'tGeo.altitude as tAlt'
            )
            ->get();

        $headers = array(
            'sId' => 'Ego',
            'rId' => 'Alter',
            'var_name' => 'Question',
            'sLat' => 'Source Latitude',
            'sLong' => 'Source Longitude',
            'sAlt' => 'Source Altitude',
            'tLat' => 'Target Latitude',
            'tLong' => 'Target Longitude',
            'tAlt' => 'Target Altitude'
        );

        $rows = array_map(function ($r) use ($headers) {
            $newRow = array();
            foreach ($headers as $key => $name){
                $newRow[$key] = $r->$key;
            }
            return $newRow;
        }, $edges);

         $filePath = storage_path() ."/app/". $fileId . '.csv';

        FileService::writeCsv($headers, $rows, $filePath);

    }

    public static function createRespondentExport($studyId, $fileId, $maxGeoTreeDepth=4){

        $startTime = microtime(true);

        $respondents = DB::table('respondent')
            ->join('study_respondent', 'study_respondent.respondent_id', '=', 'respondent.id')
            ->where('study_respondent.study_id', '=', $studyId)
            ->whereNull('respondent.deleted_at')
            ->select('respondent.id',
                'respondent.name as rname',
                'respondent.created_at',
                'respondent.updated_at',
                'respondent.geo_id')
            ->get();

        $defaultHeaders = array(
            'id' => "Respondent id",
            'rname' => "Respondent name",
            'created_at' => "Created at",
            'updated_at' => "Updated at",
        );

        $getGeoParent = Memoization::memoize(function($id){
            return DB::table('geo')
                ->join('geo_type', 'geo_type.id', '=', 'geo.geo_type_id')
                ->join('translation_text', 'translation_text.translation_id', '=', 'geo.name_translation_id')
                ->where('geo.id', '=', $id)
                ->select('geo.id', 'translation_text.translated_text as name', 'geo_type.name as type', 'geo.latitude', 'geo.longitude', 'geo.altitude', 'geo.parent_id')
                ->first();
        });

        $traverseGeoTree = function ($startingId, $maxDepth) use ($getGeoParent){
            $tree = array();
            $id = $startingId;
            while(count($tree) < $maxDepth && $id !== null){
                $parent = $getGeoParent($id);
                if($parent !== null) {
                    array_push($tree, $parent);
                    $id = $parent->parent_id;
                } else {
                    break;
                }
            }
            return $tree;
        };

        $headers = array();
        $headers = array_replace($headers, $defaultHeaders);

        $conditionsGroupedByRespondentId = DB::table('respondent_condition_tag')
            ->join('respondent', 'respondent.id', '=', 'respondent_condition_tag.respondent_id')
            ->join('condition_tag', 'condition_tag.id', '=', 'respondent_condition_tag.condition_tag_id')
            ->select('respondent.id', DB::raw("group_concat(condition_tag.name SEPARATOR ';') as conditions"))
            ->groupBy('respondent.id');

        $respondent_conditions = array_reduce($conditionsGroupedByRespondentId->get(), function($agg, $r){
            $agg[$r->id] = explode(';', $r->conditions);
            return $agg;
        }, array());

        // map each respondent to a single row of the csv
        $rows = array_map(function ($respondent) use ($defaultHeaders, &$headers, &$respondent_conditions, $maxGeoTreeDepth, $traverseGeoTree) {
            $newRow = array();
            foreach ($defaultHeaders as $key => $name){
                $newRow[$key] = $respondent->$key;
            }

            $geoTree = $traverseGeoTree($respondent->geo_id, $maxGeoTreeDepth);
            foreach($geoTree as $level => $geo){
                $key = "geo_level_" . $level;
                $headers[$key] = $key;
                $newRow[$key] = $geo->name;
            }

            // Add conditions if there are any for this respondent
            if(array_key_exists($respondent->id, $respondent_conditions)) {
                $conditions = $respondent_conditions[$respondent->id];
                foreach ($conditions as $condition) {
                    $headers[$condition] = $condition;
                    $newRow[$condition] = true;
                }
            }

            return $newRow;
        }, $respondents);


        $filePath = storage_path("app/") . $fileId . '.csv';
        FileService::writeCsv($headers, $rows, $filePath);
        $duration = microtime(true) - $startTime;
        Log::debug("createRespondentExport took $duration seconds");

    }


    /**
     * Zero padded numbers
     */
    public static function zeroPad($n){
        return str_pad($n + 1, 2, "0", STR_PAD_LEFT);
    }


    public static function getFormQuestions($formId){
        return DB::table('question')
            ->join('section_question_group', 'question.question_group_id', '=', 'section_question_group.question_group_id')
            ->join('form_section', 'section_question_group.section_id', '=', 'form_section.section_id')
            ->join('form', 'form_section.form_id', '=', 'form.id')
            ->join('question_type', 'question.question_type_id', '=', 'question_type.id')
            ->where('form.id', '=', $formId)
            ->whereNull('question.deleted_at')
            ->select('question.id',
                'question.var_name',
                'question_type.name as qtype',
                'form_section.follow_up_question_id')
            ->get();
    }

    public static function getFormSurveys($formId){
        return DB::table('survey')
            ->where('survey.form_id', '=', $formId)
            ->whereNull('survey.deleted_at')
            ->get();
    }

    public static function getRosterRows($surveyId, $questionId){
        $rows = DB::table('datum')
            ->where('datum.survey_id', '=', $surveyId)
            ->whereNull('datum.deleted_at')
            ->where('datum.val', 'not like', 'roster%')
            ->where('datum.question_id', '=', $questionId)
            ->get();

        return $rows;

    }

    public static function getQuestionDatum($surveyId, $questionId){
        return DB::table('datum')
            ->where('datum.survey_id', '=', $surveyId)
            ->where('datum.question_id', '=', $questionId)
            ->get();
    }

    public static function formatSurveyData(&$survey, &$tree, &$treeMap, &$questionsMap){
        $headers = array();
        $data = array();
        $alreadyHandledQuestions = array();


        foreach($treeMap as $qId => $children){

            if(array_key_exists($qId, $alreadyHandledQuestions))
                continue;

            $question = $questionsMap[$qId];
            if($question->qtype === 'roster'){
                $rosterRows = ReportService::getRosterRows($survey->id, $qId);

                // Add each roster row
                foreach($rosterRows as $index=>$row){
                    $key = $qId . '___' . $index;
                    $headers[$key] = $question->var_name . '_r' . ReportService::zeroPad($index);
                    $data[$key] = $row->val;
                }

                foreach($children as $cId => $cChildren){
                    $alreadyHandledQuestions[$cId] = true;
                    foreach($rosterRows as $index => $rosterRow){
                        $repeatString = '_r' . ReportService::zeroPad($index);
                        $childQuestion = $questionsMap[$cId];
                        list($newHeaders, $newData) = ReportService::handleQuestion($survey->id, $childQuestion, $repeatString);
                        $headers = array_replace($headers, $newHeaders);
                        $data = array_replace($data, $newData);
                    }
                }

            } else {
                list($newHeaders, $newData) = ReportService::handleQuestion($survey->id, $question);
                $headers = array_replace($headers, $newHeaders);
                $data = array_replace($data, $newData);
            }

        }

        return array($headers, $data);

    }

    public static function handleQuestion($studyId, $question, $repeatString=''){

        switch($question->qtype){
            case 'multiple_select':
                return ReportService::handleMultiSelect($studyId, $question, $repeatString);
            case 'geo':
                return ReportService::handleGeo($studyId, $question, $repeatString);
            default:
                return ReportService::handleDefault($studyId, $question, $repeatString);
        }

    }

    public static function handleDefault($surveyId, $question, $repeatString){

        $datum = ReportService::firstDatum($surveyId, $question->id);
        $key = $question->id . $repeatString;
        $name = $question->var_name . $repeatString;

        $headers = array($key=>$name);
        $data = $datum !== null ? array($key=>$datum->val) : array();

        return array($headers, $data);

    }

    public static function handleMultiSelect($surveyId, $question, $repeatString){

        $headers = array();
        $data = array();

        $parentDatum = ReportService::firstDatum($surveyId, $question->id);
        $possibleChoices = DB::table('question_choice')
            ->join('choice', 'choice.id', '=', 'question_choice.choice_id')
            ->where('question_choice.question_id', '=', $question->id)
            ->get();

        // Add headers for all possible choices
        foreach ($possibleChoices as $choice){
            $key = $question->id . '___' . $repeatString . $choice->val;
            $headers[$key] = $question->var_name . '_' . $choice->val . $repeatString;
        }


        // Add selected choices if there is a parent datum
        if($parentDatum !== null){
            $selectedChoices = DB::table('datum_choice')
                ->join('choice', 'datum_choice.choice_id', '=', 'choice.id')
                ->join('question_choice', 'choice.id', '=', 'question_choice.choice_id')
                ->where('datum_choice.datum_id', '=', $parentDatum->id)
                ->get();

            // Add data for all selected choices
            foreach ($selectedChoices as $choice) {
                $key = $question->id . '___' . $repeatString . $choice->val;
                $data[$key] = true;
            }
        }

        return array($headers, $data);

    }

    public static function firstDatum($surveyId, $questionId){
        return DB::table('datum')
            ->where('datum.survey_id', '=', $surveyId)
            ->where('datum.question_id', '=', $questionId)
            ->first();
    }


    public static function handleGeo($surveyId, $question, $repeatString){

        $headers = array();
        $data = array();
        $geoDatum = ReportService::firstDatum($surveyId, $question->id);

        if($geoDatum) {
            $geoData = DB::table('datum_geo')
                ->join('geo', 'datum_geo.geo_id', '=', 'geo.id')
                ->join('geo_type', 'geo_type.id', '=', 'geo.geo_type_id')
                ->where('datum_geo.datum_id', '=', $geoDatum->id)
                ->select('geo_type.name', 'geo.latitude', 'geo.longitude', 'geo.altitude')
                ->get();


            foreach ($geoData as $index => $geo) {
                foreach (array('name', 'latitude', 'longitude', 'altitude') as $name) {
                    $key = $question->id . $repeatString . '_g' . $index . '_' . $name;
                    $headers[$key] = $question->var_name . $repeatString . '_g' . ReportService::zeroPad($index) . '_' . $name;
                    $data[$key] = $geo->$name;
                }
            }
        }

        return array($headers, $data);

    }






    public static function buildFormTree($questions){

        $tree = array();
        $treeMap = array();

        // Add the base level questions to the tree
        foreach ($questions as $q) {
            if ($q->follow_up_question_id === null) {
                $tree[$q->id] = [];
                $treeMap[$q->id] = &$tree[$q->id];
            }
        }


        // Iterate adding nested values until no more nested values are found
        $foundNested = true;
        while ($foundNested) {
            $foundNested = false;
            foreach ($questions as $q) {
                if (!array_key_exists($q->id, $treeMap)
                    && $q->follow_up_question_id !== null
                    && array_key_exists($q->follow_up_question_id, $treeMap)) {
                    $n = [];
                    $treeMap[$q->follow_up_question_id][$q->id] = &$n;
                    $treeMap[$q->id] = &$n;
                    $foundNested = true;
                }
            }
        }

        return array($tree, $treeMap);

    }


    /**
     * Create an csv file with one row per survey filled out for a single formId
     * @param $formId - Id of the form to export
     * @return string - The name of the file that was exported. The file is stored in 'storage/app'
     */
    public static function createFormExport($formId, $fileId){

        $questions = ReportService::getFormQuestions($formId);

        $questionsMap = array_reduce($questions, function($agg, &$q){
            $agg[$q->id] = $q;
            return $agg;
        }, array());

        $defaultColumns = array(
            'id' => 'survey_id',
            'respondent_id' => 'respondent_id',
            'created_at' => 'created_at',
            'completed_at' => 'completed_at'
        );

        // 1. Create tree with follow up questions nested or if roster type then have the rows nested an follow up questions nested for each row
        // 2. Add any questions without follow ups
        // 3. Flatten the tree into a single

        list($tree, $treeMap) = ReportService::buildFormTree($questions);

        $headers = array();
        $rows = array();

        $surveys = ReportService::getFormSurveys($formId);

        foreach($surveys as $survey){

            list($surveyHeaders, $data) = ReportService::formatSurveyData($survey, $tree, $treeMap, $questionsMap);

            $headers = array_replace($headers, $surveyHeaders);

            // Add survey default values
            foreach($defaultColumns as $key=>$name){
                $data[$key] = $survey->$key;
            }

            array_push($rows, $data);

        }

        // Sort non default columns first then add default columns
        asort($headers);
        $headers = $defaultColumns + $headers; // add at the beginning of the array

        $filePath = storage_path() ."/app/". $fileId . '.csv';
        FileService::writeCsv($headers, $rows, $filePath);

    }


    public static function handleDatum($datum, &$row, &$headersMap, &$multiSelectQuestionsMap, &$geoQuestions, &$rosterQuestions){

        if(array_key_exists($datum->question_id, $rosterQuestions)){
            ReportService::handleRoster($datum, $row);
        } else if (array_key_exists($datum->question_id, $multiSelectQuestionsMap)) {
            ReportService::handleMultiSelect($datum, $row);
        } else if(array_key_exists($datum->question_id, $geoQuestions)){
            $geoQuestion = $geoQuestions[$datum->question_id];
            ReportService::handleGeo($datum, $row, $headersMap, $geoQuestion);
        } else {
            $row[$datum->question_id] = $datum->val;
        }

    }

}
