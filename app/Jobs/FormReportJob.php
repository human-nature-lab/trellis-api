<?php

namespace App\Jobs;

use App\Models\Datum;
use App\Models\Question;
use App\Models\QuestionDatum;
use App\Models\Translation;
use Illuminate\Support\Facades\DB;
use League\Flysystem\Exception;
use Log;
use App\Models\Report;
use App\Services\ReportService;
use Throwable;

class FormReportJob extends Job
{

    protected $formId;
    protected $report;
    protected $config;
    protected $images = [];
    protected $rows = [];
    protected $headers = [];
    protected $metaHeaders = [];
    protected $metaRows = [];
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
        } catch(Throwable $e){
            $this->report->status = 'failed';
            Log::debug("Form export $this->formId failed");
            Log::error(json_encode($e));
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

        $questions = Question::join('section_question_group', 'question.question_group_id', '=', 'section_question_group.question_group_id')
            ->join('form_section', 'section_question_group.section_id', '=', 'form_section.section_id')
            ->whereNull('question.deleted_at')
            ->whereNull('section_question_group.deleted_at')
            ->whereNull('form_section.deleted_at')
            ->orderBy('form_section.sort_order', 'section_question_group.question_group_order', 'question.sort_order')
            ->select('question.*', 'form_section.follow_up_question_id', 'form_section.is_repeatable')
            ->with('choices')
            ->get();

        $questionsMap = [];
        foreach ($questions as $question) {
            $question->has_follow_up = isset($question->follow_up_question_id);
            $questionsMap[$question->id] = $question;
        }

        $surveys = ReportService::getFormSurveys($this->formId);
        $rows = [];

        foreach($surveys as $survey){
            $row = $this->formatSurveyData($survey, $questions, $questionsMap);
            foreach($this->defaultColumns as $key=>$name){
                $row[$key] = $survey->$key;
            }
            array_push($rows, $row);
        }

        // Sort non default columns first then add default columns
        $this->headers = $this->defaultColumns + $this->headers; // add at the beginning of the array

        foreach ($rows as &$row) {
            foreach ($row as $key => $cell) {
                if (is_array($cell)) {
                    $row[$key] = implode(';', $cell);
                }
            }
        }

        ReportService::saveDataFile($this->report, $this->headers, $rows);
        ReportService::saveMetaFile($this->report, $this->metaRows);
        ReportService::saveDataFile($this->report, $this->otherHeaders, $this->otherRows, 'other');
        ReportService::saveDataFile($this->report, $this->notesHeaders, $this->notesRows, 'notes');
        ReportService::saveImagesFile($this->report, $this->images);

    }

    private function translationToText (Translation $t, String $localeId = null) {
        if (!isset($t->translationText) || $t->translationText->count() === 0) {
            return '[No text for this translation]';
        }
        $text = null;
        if ($localeId) {
            foreach ($t->translationText as $tt) {
                if ($tt->locale_id === $localeId) {
                    $text = $tt->translated_text;
                }
            }
        }

        if (is_null($text)) {
            $text = $t->translationText[0]->translated_text;
        }

        return $text;
    }

    private function getDatumValue (Datum $datum, String $localeId = null) {
        if (isset($datum->roster)) {
            return $datum->roster->val;
        } else if (isset($datum->choice)) {
            return $this->translationToText($datum->choice->choiceTranslation, $localeId);
        } else if (isset($datum->geo)) {
            return $this->translationToText($datum->geo->nameTranslation, $localeId);
        } else if (isset($datum->edge)) {
            return $datum->edge->target_respondent_id;
        } else if (isset($datum->photo)) {
            // TODO: Store photos meta data here
            return $datum->photo->file_name;
        } else {
            return $datum->val;
        }
    }

    private function formatSurveyData ($survey, $questions, $questionsMap) {
        $questionDatum = QuestionDatum::where('survey_id', $survey->id)->with('fullData');
        Log::info($questionDatum->toSql());
        $questionDatum = $questionDatum->get();
        $row = [];

        // Create indexes
        $questionToQuestionDatumMap = [];
        $datumMap = [];
        foreach ($questionDatum as $qd) {
            if (!isset($questionToQuestionDatumMap[$qd->question_id])) {
                $questionToQuestionDatumMap[$qd->question_id] = [];
            }
            array_push($questionToQuestionDatumMap[$qd->question_id], $qd);
            foreach ($qd->fullData as $datum) {
                $datumMap[$datum->id] = $datum; // For looking up follow up info
            }
        }

        // Index the question order
        $questionOrderMap = [];
        foreach ($questions as $index => $question) {
            $questionOrderMap[$question->id] = $index;
        }

        // Sort question datum
        foreach ($questionDatum as $qd) {
            $qd->fullData->sortBy('sort_order');
        }

        // TODO: Make complete form test to use for exporting. Repeated sections with each question type

        // Make sure the $questionDatum are all ordered the same way
        $questionDatum = $questionDatum->sort(function (QuestionDatum $a,  QuestionDatum $b) use ($questionOrderMap) {
            if ($a->question_id === $b->question_id) {
                // TODO: Handle repeated sections and follow up sections
                return 0;
            } else {
                return $questionOrderMap[$a->question_id] - $questionOrderMap[$b->question_id];
            }
        });

        foreach ($questionDatum as $qd) {
            $question = $questionsMap[$qd->question_id];
            $baseKey = $question->id; // TODO: Add repetitions
            $baseName = $question->var_name;
            if ($question->has_follow_up) {
                $datum = $datumMap[$qd->follow_up_datum_id];
                $baseKey .= '_r' . $datum->sort_order;
                $baseName .= '_r' . ReportService::zeroPad($datum->sort_order);
            }
            switch ($question->questionType->name) {
                case 'multiple_select':
                    // Make all of the choices their own column
                    foreach ($question->choices as $choice) {
                        $key = $baseKey . '_' . $choice->id;
                        $this->headers[$key] = $baseName . '_' . $choice->val;
                        $this->metaRows[$this->headers[$key]] = [
                            'header' => $this->headers[$key],
                            'question_type' => $question->questionType->name,
                            'variable_name' => $question->var_name,
                            'option_code' => $choice->val,
                            'option_id' => $choice->id
                        ];
                        foreach ($choice->choiceTranslation->translationText as $tt) {
                            $metaData[$this->headers[$key]]["option_$tt->language_name"] = $tt->translated_text;
                        }
                        foreach ($question->questionTranslation->translationText as $tt) {
                            $metaData[$this->headers[$key]]["question_$tt->language_name"] = $tt->translated_text;
                        }
                    }
                    foreach ($qd->fullData as $datum) {
                        $key = $baseKey . '_' . $datum->choice->id;
                        // TODO: Handle showing crosswalk as true or false instead of values
                        if (isset($qd->dk_rf)) {
                            $row[$key] = $qd->dk_rf ? 'DK' : 'RF';
                        } else {
                            $row[$key] = $this->translationToText($datum->choice->choiceTranslation, $this->config->locale);
                        }

                        // This seems like the safest way to check if it's an other response
                        if (isset($datum->val) && count($datum->val) > 0 && isset($datum->choice_id)) {
                            $this->addOther($this->headers[$key], $survey->id, $survey->respondent_id, $datum->val);
                        }
                    }

                    if (isset($qd->dk_rf)) {
                        $this->addNote($this->headers[$baseKey], $survey, $qd->dk_rf, $qd->dk_rf_val);
                    }

                    break;
                default:
                    $key = $baseKey;
                    $this->headers[$key] = $baseName;
                    $vals = [];
                    $this->metaRows[$this->headers[$key]] = [
                        'header' => $this->headers[$key],
                        'question_type' => $question->questionType->name,
                        'variable_name' => $question->var_name
                    ];
                    foreach ($question->questionTranslation->translationText as $t) {
                        $this->metaRows[$this->headers[$key]]["question_$t->language_name"] = $t->translated_text;
                    }
                    if (isset($qd->dk_rf)) {
                        array_push($vals, $qd->dk_rf ? 'DK' : 'RF');
                        $this->addNote($this->headers[$key], $survey, $qd->dk_rf, $qd->dk_rf_val);
                    } else {
                        foreach ($qd->fullData as $datum) {
                            array_push($vals, $this->getDatumValue($datum, $this->config->locale));
                        }
                    }
                    $row[$key] = $vals;
            }
        }
        return $row;
    }

    private function formatSurveyDataOld ($survey, $tree, $treeMap, $questionsMap) {

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

    private function addNote($questionName, $survey, $type, $text){
        array_push($this->notesRows, [
            'question' => $questionName,
            'survey_id' => $survey->id,
            'respondent_id' => $survey->respondent_id,
            'type' => $type,
            'text' => $text
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
