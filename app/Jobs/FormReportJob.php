<?php

namespace App\Jobs;

use League\Flysystem\Exception;
use Log;
use App\Models\Report;
use App\Models\ReportFile;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\ReportService;
use App\Services\FileService;
use Ramsey\Uuid\Uuid;

class FormReportJob extends Job
{
    use InteractsWithQueue, SerializesModels;

    protected $formId;
    protected $report;
    protected $config;
    protected $images=array();
    protected $rows=array();
    protected $headers=array();
    protected $defaultColumns;

    /**
     * Create a new job instance.
     *
     * @param  $formId
     * @return void
     */
    public function __construct($formId, $fileId, $config)
    {
        Log::debug("FormReportJob - constructing: $formId");
        $this->config = $config;
        $this->formId = $formId;
        $this->report = new Report();
        $this->report->id = $fileId;
        $this->report->type = 'form';
        $this->report->status = 'queued';
        $this->report->report_id = $this->formId;
        $this->report->save();
    }

    public function handle()
    {
        $startTime = microtime(true);
        Log::debug("FormReportJob - handling: $this->formId, $this->report->id");
        try{
            $this->create();
            $this->report->status = 'saved';
        } catch(Exception $e){
            $this->report->status = 'failed';
            Log::debug("Form export $this->formId failed");
        } finally{
            $this->report->save();
            $duration = microtime(true) - $startTime;
            Log::debug("FormReportJob - finished: $this->formId in $duration seconds");
        }

    }

    /**
     * Actually create the FormReport
     */
    private function create(){

        $questions = ReportService::getFormQuestions($this->formId);

        $questionsMap = array_reduce($questions, function($agg, &$q){
            $agg[$q->id] = $q;
            return $agg;
        }, array());

        $this->defaultColumns = array(
            'id' => 'survey_id',
            'respondent_id' => 'respondent_id',
            'created_at' => 'created_at',
            'completed_at' => 'completed_at'
        );

        // 1. Create tree with follow up questions nested or if roster type then have the rows nested an follow up questions nested for each row
        // 2. Add any questions without follow ups
        // 3. Flatten the tree into a single

        list($tree, $treeMap) = ReportService::buildFormTree($questions);

        $surveys = ReportService::getFormSurveys($this->formId);

        foreach($surveys as $survey){

            $this->formatSurveyData($survey, $tree, $treeMap, $questionsMap);

        }

        // Sort non default columns first then add default columns
        asort($this->headers);
        $this->headers = $this->defaultColumns + $this->headers; // add at the beginning of the array

        ReportService::saveDataFile($this->report, $this->headers, $this->rows);

        ReportService::saveImagesFile($this->report, $this->images);
//        $this->generateImagesZip();

    }


    private function formatSurveyData($survey, $tree, $treeMap, $questionsMap){

        $row = array();
        $alreadyHandledQuestions = array();

        foreach($treeMap as $qId => $children){

            if(array_key_exists($qId, $alreadyHandledQuestions))
                continue;

            $question = $questionsMap[$qId];
            if($question->qtype === 'roster'){
                $rosterRows = ReportService::getRosterRows($survey->id, $qId);

                // Add each roster row
                foreach($rosterRows as $index=>$rosterRow){
                    $key = $qId . '___' . $index;
                    $this->headers[$key] = $question->var_name . '_r' . ReportService::zeroPad($index);
                    $row[$key] = $rosterRow->val;
                }

                foreach($children as $cId => $cChildren){
                    $alreadyHandledQuestions[$cId] = true;
                    foreach($rosterRows as $index => $rosterRow){
                        $repeatString = '_r' . ReportService::zeroPad($index);
                        $childQuestion = $questionsMap[$cId];
                        list($headers, $vals) = $this->handleQuestion($survey->id, $childQuestion, $repeatString);
                        $this->headers = array_replace($this->headers, $headers);
                        $row = array_replace($row, $vals);
                    }
                }

            } else {
                list($headers, $vals) = $this->handleQuestion($survey->id, $question);
                $this->headers = array_replace($this->headers, $headers);
                $row = array_replace($row, $vals);
            }

        }


        // Add survey default values
        foreach($this->defaultColumns as $key=>$name){
            $row[$key] = $survey->$key;
        }

        array_push($this->rows, $row);

    }


    private function handleQuestion($studyId, $question, $repeatString=''){

        $images = array();

        switch($question->qtype){
            case 'multiple_select':
                list($headers, $vals) = ReportService::handleMultiSelect($studyId, $question, $repeatString, $this->config->useChoiceNames, $this->config->locale);
                break;
            case 'geo':
                list($headers, $vals) = ReportService::handleGeo($studyId, $question, $repeatString);
                break;
            case 'image':
                list($headers, $vals, $images) = ReportService::handleImage($studyId, $question, $repeatString);
                break;
            case 'multiple_choice':
                list($headers, $vals) = ReportService::handleMultiChoice($studyId, $question, $repeatString, $this->config->useChoiceNames, $this->config->locale);
                break;
            default:
                list($headers, $vals) = ReportService::handleDefault($studyId, $question, $repeatString);
                break;
        }

        $this->images = array_merge($this->images, $images);

        return array($headers, $vals);

    }

}