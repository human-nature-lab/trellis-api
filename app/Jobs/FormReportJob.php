<?php

namespace App\Jobs;

use Illuminate\Support\Facades\DB;
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
//    use InteractsWithQueue, SerializesModels;

    protected $formId;
    protected $report;
    protected $config;
    protected $images=[];
    protected $rows=[];
    protected $headers=[];
    protected $metaHeaders=[];
    protected $metaRows=[];
    protected $otherRows = [];
    protected $otherHeaders = [];
    protected $notesRows = [];
    protected $notesHeaders = [];
    protected $defaultColumns;

    /**
     * FormReportJob constructor.
     * @param $formId - ID of the form we're exporting
     * @param $reportId - The id of the report we're generating. This is used on the client side to check if the report has finished exporting
     * @param $config - Any configuration options used to generate this report
     */
    public function __construct($formId, $reportId, $config)
    {
        Log::debug("FormReportJob - constructing: $formId");
        $this->config = $config;
        $this->formId = $formId;
        $this->report = new Report();
        $this->report->id = $reportId;
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

        $questions = ReportService::getFormQuestions($this->formId, $this->config->locale);

        $questionsMap = array_reduce($questions, function($agg, &$q){
            $agg[$q->id] = $q;
            return $agg;
        }, []);

        $this->defaultColumns = [
            'id' => 'survey_id',
            'respondent_id' => 'respondent_id',
            'created_at' => 'created_at',
            'completed_at' => 'completed_at'
        ];

        $this->otherHeaders = [
            'question' => 'question',
            'survey_id' => "survey_id",
            'respondent_id' => "respondent_id",
            'text' => "response"
        ];

        $this->notesHeaders = [
            'question' => 'question',
            'survey_id' => 'survey_id',
            'respondent_id' => 'respondent_id',
            'text' => 'response'
        ];

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
        ReportService::saveMetaFile($this->report, $this->metaRows);
        ReportService::saveDataFile($this->report, $this->otherHeaders, $this->otherRows, 'other');
        ReportService::saveDataFile($this->report, $this->notesHeaders, $this->notesRows, 'notes');
        ReportService::saveImagesFile($this->report, $this->images);

    }


    private function formatSurveyData($survey, $tree, $treeMap, $questionsMap){

        $row = [];
        $alreadyHandledQuestions = [];

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
                    $this->metaRows[$this->headers[$key]] = [
                        'header' => $this->headers[$key],
                        'variable_name' => $question->var_name,
                        'question_type' => $question->qtype,
                    ];
                    $row[$key] = $rosterRow->val;
                }

                foreach($children as $cId => $cChildren){
                    $alreadyHandledQuestions[$cId] = true;
                    foreach($rosterRows as $index => $rosterRowDatum){
                        $repeatString = '_r' . ReportService::zeroPad($index);
                        $childQuestion = $questionsMap[$cId];
                        list($headers, $vals, $metaRows) = $this->handleQuestion($survey, $childQuestion, $repeatString, $rosterRowDatum->id);
                        $this->headers = array_replace($this->headers, $headers);
                        $this->metaRows = array_merge($this->metaRows, $metaRows);
                        $row = array_replace($row, $vals);
                    }
                }

            } else {
                list($headers, $vals, $metaRows) = $this->handleQuestion($survey, $question);
                $this->headers = array_replace($this->headers, $headers);
                $this->metaRows = array_replace($this->metaRows, $metaRows);
                $row = array_replace($row, $vals);
            }

        }


        // Add survey default values
        foreach($this->defaultColumns as $key=>$name){
            $row[$key] = $survey->$key;
        }

        array_push($this->rows, $row);

    }


    private function handleQuestion($survey, $question, $repeatString='', $rowDatumId=null){

        $images = [];
        switch($question->qtype){
            case 'multiple_select':
                list($headers, $vals, $metaData) = self::handleMultiSelect($survey, $question, $repeatString, $this->config->useChoiceNames, $this->config->locale, $rowDatumId);
                break;
            case 'geo':
                list($headers, $vals, $metaData) = self::handleGeo($survey, $question, $repeatString, $this->config->locale, $rowDatumId);
                break;
            case 'image':
                list($headers, $vals, $images, $metaData) = self::handleImage($survey, $question, $repeatString, $rowDatumId);
                break;
            case 'multiple_choice':
                list($headers, $vals, $metaData) = self::handleMultiChoice($survey, $question, $repeatString, $this->config->useChoiceNames, $this->config->locale, $rowDatumId);
                break;
            default:
                list($headers, $vals, $metaData) = self::handleDefault($survey, $question, $repeatString, $rowDatumId);
                break;
        }

        $this->images = array_merge($this->images, $images);

        return [$headers, $vals, $metaData];

    }

    private static function firstDatum($surveyId, $questionId, $parentDatumId=null){
        $query = DB::table('datum')
            ->where('datum.survey_id', '=', $surveyId)
            ->where('datum.question_id', '=', $questionId)
            ->whereNull('datum.deleted_at');
        if($parentDatumId){
            $query = $query->where('datum.parent_datum_id', '=', $parentDatumId);
        }
        return $query->first();
    }

    private function addNote($questionName, $survey, $datum){
        array_push($this->notesRows, [
            'question' => $questionName,
            'survey_id' => $survey->id,
            'respondent_id' => $survey->respondent_id,
            'type' => $datum->opt_out,
            'text' => $datum->opt_out_val
        ]);
    }

    private function addOther($questionName, $surveyId, $respondentId, $response){
        array_push($this->otherRows, [
            'question' => $questionName,
            'survey_id' => $surveyId,
            'respondent_id' => $respondentId,
            'text' => $response
        ]);
    }

    public function handleImage($survey, $question, $repeatString, $parentDatumId){
        $headers = [];
        $data = [];
        $images = [];
        $metaData = [];
        $surveyId = $survey->id;

        // This is flawed because it will use the same datum for 2 rows in a roster. The imageDatum needs to reference the
        // parent datum that is the roster row as well. This is likely flawed in all of these exports and will need to be
        // changed.
        $imageDatum = self::firstDatum($surveyId, $question->id, $parentDatumId);
        if($imageDatum !== null){
            $imageData = DB::table('datum_photo')
                ->join('photo', 'datum_photo.photo_id', '=', 'photo.id')
                ->where('datum_photo.datum_id', '=', $imageDatum->id)
                ->whereNull('datum_photo.deleted_at')
                ->select('photo.file_name', 'photo.id')
                ->get();
            // TODO: Check if you can omit the $imageDatum and maybe consider doing a semi-colon delimited lsit instead
            $imageList = [];
            foreach($imageData as $index => $datum){
                array_push($imageList, $datum->file_name);
                array_push($images, $datum);
            }
            $key = $question->id.$repeatString;
            $headers[$key] = $question->var_name.$repeatString;
            $data[$key] = implode(';', $imageList);
            $metaData[$headers[$key]] = [
                'header' => $headers[$key],
                'question_type' => $question->qtype,
                'variable_name' => $question->var_name
            ];
            foreach ($question->translations as $t) {
                $metaData[$headers[$key]]["question_$t->language_name"] = $t->translated_text;
            }
        }

        // handle opted out questions
        if($imageDatum !== null && $imageDatum->opt_out !== null){
            $key = $question->id.$repeatString;
            $data[$key] = $imageDatum->opt_out;
            $this->addNote($headers[$key], $survey, $imageDatum);
        }

        return [$headers, $data, $images, $metaData];
    }

    public function handleGeo($survey, $question, $repeatString, $locale, $parentDatumId, $useAnyLocale=true){

        $headers = [];
        $data = [];
        $metaData = [];
        $surveyId = $survey->id;
        $geoDatum = self::firstDatum($surveyId, $question->id, $parentDatumId);

        if($geoDatum) {
            $geoData = DB::table('datum_geo')
                ->join('geo', 'datum_geo.geo_id', '=', 'geo.id')
                ->leftJoin('translation_text', function ($join) use ($locale) {
                    $join->on('translation_text.translation_id', '=', 'geo.name_translation_id')
                        ->whereNull('translation_text.deleted_at')
                        ->on('translation_text.locale_id', '=', DB::raw("'" . $locale . "'"));
                })->where('datum_geo.datum_id', '=', $geoDatum->id)
                ->whereNull('datum_geo.deleted_at')
                ->select('translation_text.translated_text as name', 'geo.id', 'geo.name_translation_id as tId');

            foreach ($geoData->get() as $index => $geo) {
                // Get the next geo
                if($geo->name === null && $useAnyLocale){
                    $geoTranslation = DB::table('translation_text')
                        ->where('translation_text.translation_id', '=', $geo->tId)
                        ->first();
                    $geo->name = $geoTranslation->translated_text;
                }
                foreach (['name', 'id'] as $name) {
                    $key = $question->id . $repeatString . '_g' . $index . '_' . $name;
                    $headers[$key] = $question->var_name . $repeatString . '_g' . ReportService::zeroPad($index) . '_' . $name;
                    $metaData[$headers[$key]] = [
                        'header' => $headers[$key],
                        'question_type' => $question->qtype,
                        'variable_name' => $question->var_name
                    ];
                    foreach ($question->translations as $t) {
                        $metaData[$headers[$key]]["question_$t->language_name"] = $t->translated_text;
                    }
                    $data[$key] = $geo->$name;
                }
            }
        }

        return [$headers, $data, $metaData];

    }

    public function handleDefault($survey, $question, $repeatString, $parentDatumId){

        $surveyId = $survey->id;
        $datum = self::firstDatum($surveyId, $question->id, $parentDatumId);
        $key = $question->id . $repeatString;
        $name = $question->var_name . $repeatString;

        $headers = [$key=>$name];
        $data = [];
        if($datum !== null && $datum->opt_out !== null) {
            $data[$key] = $datum->opt_out;
            $this->addNote($headers[$key], $survey, $datum);
        } else if($datum !== null){
            $data[$key] = $datum->val;
        }

        $metaData = [$headers[$key] => [
            'header' => $headers[$key],
            'question_type' => $question->qtype,
            'variable_name' => $question->var_name
        ]];
        foreach ($question->translations as $t) {
            $metaData[$headers[$key]]["question_$t->language_name"] = $t->translated_text;
        }

        return array($headers, $data, $metaData);

    }

    public function handleMultiChoice($survey, $question, $repeatString, $useChoiceNames=false, $locale, $parentDatumId=null){
        $surveyId = $survey->id;
        $query = DB::table('datum')
            ->leftJoin('datum_choice', function($join){
                $join->on('datum_choice.datum_id', '=', 'datum.id');
                $join->whereNull('datum_choice.deleted_at');
            })
            ->leftJoin('choice', 'choice.id', '=', 'datum_choice.choice_id')
            ->leftJoin('translation_text', function($join) use ($locale) {
                $join->on('translation_text.translation_id', '=', 'choice.choice_translation_id');
                $join->on('translation_text.locale_id', '=', DB::raw("'".$locale."'"));
            })
            ->where('datum.survey_id', '=', $surveyId)
            ->where('datum.question_id', '=', $question->id)
            ->whereNull('datum.deleted_at')
            ->select('datum.val', 'datum.name', 'translation_text.translated_text as translated_val', 'datum.opt_out', 'datum_choice.override_val', 'datum.opt_out_val');

        if($parentDatumId){
            $query = $query->where('datum.parent_datum_id', '=', $parentDatumId);
        }

        $datum = $query->first();
        $key = $question->id . $repeatString;
        $name = $question->var_name . $repeatString;
        $headers = [$key=>$name];
        $data = [];
        $metaData[$headers[$key]] = [
            'header' => $headers[$key],
            'question_type' => $question->qtype,
            'variable_name' => $question->var_name
        ];
        foreach ($question->translations as $t) {
            $metaData[$headers[$key]]["question_$t->language_name"] = $t->translated_text;
        }
        if($datum !== null){
            if($datum->opt_out !== null){
                $data[$key] = $datum->opt_out;
                $this->addNote($headers[$key], $survey, $datum);
            } else if($useChoiceNames){
                $data[$key] = $datum->translated_val;
            } else {
                $data[$key] = $datum->val;
            }
            if($datum->override_val){
                $this->addOther($headers[$key], $surveyId, $survey->respondent_id, $datum->override_val);
            }
        }

        return [$headers, $data, $metaData];

    }

    public function handleMultiSelect($survey, $question, $repeatString, $useChoiceNames=false, $locale, $parentDatumId){

        $surveyId = $survey->id;
        $headers = [];
        $data = [];
        $metaData = [];

        $parentDatum = self::firstDatum($surveyId, $question->id, $parentDatumId);
        $possibleChoices = ReportService::getQuestionChoices($question, $locale);

        // Add headers for all possible choices
        foreach ($possibleChoices as $choice){
            $key = $question->id . '___' . $repeatString . $choice->val;
            $headers[$key] = $question->var_name . $repeatString . '_' . $choice->val;
            $metaData[$headers[$key]] = [
                'header' => $headers[$key],
                'variable_name' => $question->var_name,
                'question_type' => $question->qtype,
                'option_code' => $choice->val,
                'option_id' => $choice->id
            ];
            foreach ($question->translations as $t) {
                $metaData[$headers[$key]]["question_$t->language_name"] = $t->translated_text;
            }
            foreach ($choice->translations as $t) {
                $metaData[$headers[$key]]["option_$t->language_name"] = $t->translated_text;
            }
        }

        // Add selected choices if there is a parent datum
        if($parentDatum !== null){
            $selectedChoices = DB::table('datum_choice')
                ->join('choice', 'datum_choice.choice_id', '=', 'choice.id')
                ->join('question_choice', 'choice.id', '=', 'question_choice.choice_id')
                ->leftJoin('translation_text', function($join) use ($locale)
                {
                    $join->on('translation_text.translation_id', '=', 'choice.choice_translation_id')
                        ->on('translation_text.locale_id', '=', DB::raw("'".$locale."'"));
                })
                ->where('datum_choice.datum_id', '=', $parentDatum->id)
                ->whereNull('datum_choice.deleted_at')
                ->select('choice.val', 'choice.id', 'translation_text.translated_text as name', 'datum_choice.override_val')
                ->get();

            // Add data for all selected choices
            foreach ($selectedChoices as $choice) {
                $key = $question->id . '___' . $repeatString . $choice->val;
                if($useChoiceNames){
                    $data[$key] = $choice->name;
                } else{
                    $data[$key] = true;
                }
                if($choice->override_val){
                    $this->addOther($headers[$key], $surveyId, $survey->respondent_id, $choice->override_val);
                }
            }
        }

        // Handle opted_out questions
        if($parentDatum !== null) {
            if ($parentDatum->opt_out !== null){
                $key = $question->id . '___' . $repeatString;
                $headers[$key] = $question->var_name . $repeatString;
                $data[$key] = $parentDatum->opt_out;
                $this->addNote($headers[$key], $survey, $parentDatum);
            }
        }



        return [$headers, $data, $metaData];

    }

}
