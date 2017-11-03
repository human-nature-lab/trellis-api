<?php

namespace App\Services;

use Log;
use App\Models\Form;
use App\Models\Datum;
use App\Models\Survey;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\DB;
use App\Models\Question;
use App\Services\FileService;

class ExportService
{

    public static function createEdgesExport($studyId){

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

        $uuid = Uuid::uuid4();
        $fileName = "$uuid.csv";
        $filePath = storage_path() ."/app/". $fileName;

        FileService::writeCsv($headers, $rows, $filePath);

        return $fileName;

    }

    public static function createRespondentExport($studyId){

        $respondents = DB::table('respondent')
            ->join('study_respondent', 'study_respondent.respondent_id', '=', 'respondent.id')
            ->join('geo', 'geo.id', '=', 'respondent.geo_id')
            ->join('geo_type', 'geo_type.id', '=', 'geo_type_id')
            ->where('study_respondent.study_id', '=', $studyId)
            ->whereNull('respondent.deleted_at')
            ->select('respondent.id',
                'respondent.name as rname',
                'respondent.created_at',
                'respondent.updated_at',
                'geo.altitude',
                'geo.latitude',
                'geo.longitude',
                'geo_type.name as gname')
            ->get();

        $headers = array(
            'id' => "Respondent id",
            'rname' => "Respondent name",
            'created_at' => "Created at",
            'updated_at' => "Updated at",
            'gname' => "Location name",
            'latitude' => "Location latitude",
            'longitude' => "Location longitude"
        );

        $rows = array_map(function ($r) use ($headers) {
            $newRow = array();
            foreach ($headers as $key => $name){
                $newRow[$key] = $r->$key;
            }
            return $newRow;
        }, $respondents);

        $uuid = Uuid::uuid4();
        $fileName = "$uuid.csv";
        $filePath = storage_path() ."/app/". $fileName;

        FileService::writeCsv($headers, $rows, $filePath);

        return $fileName;


    }


    /**
     * Create an csv file with one row per survey filled out for a single formId
     * @param $formId - Id of the form to export
     * @return string - The name of the file that was exported. The file is stored in 'storage/app'
     */
    public static function createFormExport($formId)
    {
        
        // Get all of the form questions (including unanswered questions)
        $form = Form::find($formId);

        $defaultColumns = array(
            'id' => 'survey_id',
            'respondent_id' => 'respondent_id',
            'completed_at' => 'completed_at'
        );

        $questions = DB::table('question')
            ->join('section_question_group', 'question.question_group_id', '=', 'section_question_group.question_group_id')
            ->join('form_section', 'section_question_group.section_id', '=', 'form_section.section_id')
            ->join('form', 'form_section.form_id', '=', 'form.id')
            ->join('question_type', 'question.question_type_id', '=', 'question_type.id')
            ->where('form.id', '=', $form->id)
            ->whereNull('question.deleted_at')
            ->select('question.id', 'question.var_name', 'question_type.name as qtype', 'form_section.follow_up_question_id')
            ->get();

        $questionsMap = array_reduce($questions, function($agg, $q){
            $agg[$q->id] = $q;
            return $agg;
        }, array());

        // Create the base headers for each question
        $headersMap = array();
        foreach ($questions as $q){
            if($q->qtype !== 'geo' && $q->qtype !== 'roster' && $q->qtype!=='multiple_select') {
                $headersMap[$q->id] = &$q->var_name;
            }
        }



        $multiSelectQuestions = array_filter($questions, function($q){
            return $q->qtype === 'multiple_select';
        });


        $geoQuestions = array_reduce(array_filter($questions, function($q){
            return $q->qtype === 'geo' ;
        }), function($agg, $q){
            $agg[$q->id] = &$q;
            return $agg;
        }, array());


        $rosterQuestions = array_reduce(array_filter($questions, function($q){
            return $q->qtype === 'roster';
        }), function($agg, $q){
            $agg[$q->id] = &$q;
            return $agg;
        }, array());


        $multiSelectQuestionsMap = array();
        foreach ($multiSelectQuestions as $q){
            $multiSelectQuestionsMap[$q->id] = true;
//            Log::debug("removing multi_select $q->id");
//            unset($headersMap[$q->id]);
        }

        // Add all possible options to header values for multi select type questions
        foreach ($multiSelectQuestions as $mq){
            $choices = DB::table('question_choice')
                        ->join('choice', 'question_choice.choice_id', '=', 'choice.id')
                        ->where('question_choice.question_id', '=', $mq->id)
                        ->whereNull('choice.deleted_at')
                        ->get();
            foreach ($choices as $choice){
                $key = $mq->id . "___" . $choice->val;
                $name = $mq->var_name . '_' . $choice->val;
                $headersMap[$key] = $name;
            }
        }


        $allSurveys = DB::table('survey')
            ->where('survey.form_id', '=', $form->id)
            ->get();



        $rows = array();
        foreach ($allSurveys as $survey){
            Log::debug("survey $survey->id");
            $row = array();
            // TODO: add zero padding

            foreach($rosterQuestions as $rosterQuestion){

                // Get all the data referencing this question
                $rosterRows = DB::table('datum')
                    ->where('datum.question_id', '=', $rosterQuestion->id)
                    ->where('datum.survey_id', '=', $survey->id)
                    ->orderBy('sort_order', 'ASC')
                    ->get();

                // Filter out the parent value
                $rosterRows = array_filter($rosterRows, function($row){
                    return $row->parent_datum_id !== null;
                });


                // Add a column for each roster row first
                foreach ($rosterRows as $index => $rosterRow){
                    $key = $rosterQuestion->id . $index;
                    $name = $rosterQuestion->var_name . '_r' . $index;
                    $headersMap[$key] = $name;
                    $row[$key] = $rosterRow->val;
                }

                // Then add a column for each follow up question
                foreach ($rosterRows as $rosterRow){

                    $followUpAnswers = DB::table('datum')
                        ->where('datum.parent_datum_id', '=', $rosterRow->id)
                        ->orderBy('sort_order', 'ASC')
                        ->get();

                    foreach ($followUpAnswers as $index => $answer){
                        $key = $answer->question_id . $index;
                        $name = $questionsMap[$answer->question_id]->var_name . '_r' . $index;
                        $headersMap[$key] = $name;
                        $row[$key] = $answer->val;
                    }

                }

            }


            // Multiple select
            // 1. Get all multiple select parent data
            // 2. Get all of the possible choices for this question
            // 3. Get all of the selected choice values
            // 4. Convert the choices into columns

             // Add any single answer data
            $datums = DB::table('datum')
                ->where('datum.survey_id', '=', $survey->id)
                ->get();
            foreach ($datums as $datum) {
                Log::debug("adding $datum->question_id, $datum->val to row");
                if (array_key_exists($datum->question_id, $multiSelectQuestionsMap)) {
                    ExportService::handleMultiSelect($datum, $row);
                } else if(array_key_exists($datum->question_id, $geoQuestions)){
                    $geoQuestion = $geoQuestions[$datum->question_id];
                    ExportService::handleGeo($datum, $row, $headersMap, $geoQuestion);
                } else {
                    $row[$datum->question_id] = $datum->val;
                }
            }


            // Add the default column values to the row
            foreach ($defaultColumns as $colId => $colName){
                $row[$colId] = $survey->{$colId};
            }

            array_push($rows, $row);
        }



        $uuid = Uuid::uuid4();
        $fileName = "$uuid.csv";
        $filePath = storage_path() ."/app/". $fileName;

        // Sort non default columns first then add default columns
        ksort($headersMap);
        $headersMap = $defaultColumns + $headersMap; // add at the beginning of the array

		FileService::writeCsv($headersMap, $rows, $filePath);

		return $fileName;

    }





    public static function handleRoster($surveyId, $rosterParent, &$row, &$headersMap, $baseKey=''){

        $rosterRows = DB::table('datum')
            ->where('datum.question_id', '=', $rosterParent->question_id)
            ->where('datum.survey_id', '=', $surveyId)
            ->orderBy('sort_order', 'ASC')
            ->get();

        // Filter out the parent value
        $rosterRows = array_filter($rosterRows, function($row){
            return $row->parent_datum_id !== null;
        });


        // Add a column for each roster row first
        foreach ($rosterRows as $index => $rosterRow){
            $key = $baseKey . $rosterParent->question_id . $index;
            $name = $headersMap[$rosterParent->question_id] . '_r' . $index;
            $headersMap[$key] = $name;
            $row[$key] = $rosterRow->val;
        }

        // Then add a column for each follow up question
        foreach ($rosterRows as $rosterRow){

            $followUpAnswers = DB::table('datum')
                ->where('datum.parent_datum_id', '=', $rosterRow->id)
                ->orderBy('sort_order', 'ASC')
                ->get();

            foreach ($followUpAnswers as $index => $answer){
                $key = $answer->question_id . $index;
                $name = $headersMap[$answer->question_id] . '_r' . $index;
                $headersMap[$key] = $name;
                $row[$key] = $answer->val;
            }

        }


    }



    public static function handleDatum($datum, &$row, &$headersMap, &$multiSelectQuestionsMap, &$geoQuestions, &$rosterQuestions){

        if(array_key_exists($datum->question_id, $rosterQuestions)){
            ExportService::handleRoster($datum, $row);
        } else if (array_key_exists($datum->question_id, $multiSelectQuestionsMap)) {
            ExportService::handleMultiSelect($datum, $row);
        } else if(array_key_exists($datum->question_id, $geoQuestions)){
            $geoQuestion = $geoQuestions[$datum->question_id];
            ExportService::handleGeo($datum, $row, $headersMap, $geoQuestion);
        } else {
            $row[$datum->question_id] = $datum->val;
        }

    }


    public static function handleMultiSelect($parentDatum, &$row){
        $selectedChoices = DB::table('datum_choice')
            ->join('choice', 'datum_choice.choice_id', '=', 'choice.id')
            ->join('question_choice', 'choice.id', '=', 'question_choice.choice_id')
            ->where('datum_id', '=', $parentDatum->id)
            ->get();

        foreach ($selectedChoices as $choice) {
            $key = $parentDatum->question_id . '___' . $choice->val;
            $row[$key] = true;
        }
    }


    public static function handleGeo($datum, &$row, &$headersMap, $geoQuestion){

        $geoDatum = DB::table('datum_geo')
            ->join('geo', 'datum_geo.geo_id', '=', 'geo.id')
            ->join('geo_type', 'geo_type.id', '=', 'geo.geo_type_id')
            ->where('datum_geo.datum_id', '=', $datum->id)
            ->select('geo_type.name', 'geo.latitude', 'geo.longitude', 'geo.altitude')
            ->get();

        foreach ($geoDatum as $index=>$geo) {
            foreach (array('name', 'latitude', 'longitude', 'altitude') as $name) {
                $key = $datum->question_id . '_' . $index . '_' . $name;
                $headersMap[$key] = $geoQuestion->var_name . '_' . $index . '_' . $name;
                $row[$key] = $geo->$name;
            }
        }

    }




}
