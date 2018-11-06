<?php

namespace App\Jobs;

use App\Classes\CsvFileStream;
use App\Models\Datum;
use App\Models\Question;
use App\Models\QuestionDatum;
use App\Models\Survey;
use App\Models\Translation;
use Illuminate\Support\Facades\DB;
use League\Flysystem\Exception;
use Log;
use App\Models\Report;
use App\Services\ReportService;
use Ramsey\Uuid\Uuid;
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
    private $file;

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
            Log::error($e);
        } finally{
            $this->report->save();
            if (isset($this->file)) {
                $this->file->close();
            }
            $duration = microtime(true) - $startTime;
            Log::debug("FormReportJob - finished: $this->formId in $duration seconds");
        }

    }

    /**
     * Actually create the FormReport
     */
    private function create(){

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
            ->join('question_group', 'question.question_group_id', '=', 'question_group.id')
            ->join('form_section', 'section_question_group.section_id', '=', 'form_section.section_id')
            ->join('section', 'section_question_group.section_id', '=', 'section.id')
            ->whereNull('section.deleted_at')
            ->whereNull('question.deleted_at')
            ->whereNull('section_question_group.deleted_at')
            ->whereNull('form_section.deleted_at')
            ->whereNull('question_group.deleted_at')
            ->where('form_section.form_id', '=', $this->formId)
            ->orderBy('form_section.sort_order', 'section_question_group.question_group_order', 'question.sort_order')
            ->select('question.*', 'form_section.follow_up_question_id', 'form_section.is_repeatable')
            ->with('choices');

        $questions = $questions->get();

        $this->makeHeaders($questions);

        $id = Uuid::uuid4();
        $fileName = $id . '.csv';
        $filePath = storage_path('app/' . $fileName);
        $this->file = new CsvFileStream($filePath, $this->headers);
        $this->file->open();
        $this->file->writeHeader();

        $questionsMap = [];
        foreach ($questions as $question) {
            $question->has_follow_up = isset($question->follow_up_question_id);
            $questionsMap[$question->id] = $question;
        }

        $page = 0;
        $pageSize = 200;
        do {
            $batch = Survey::whereNull('survey.deleted_at')
                ->where('form_id', '=', $this->formId)
                ->limit($pageSize)
                ->offset($page * $pageSize)
                ->get();
            $batchCount = $batch->count();
            Log::info("Processing $batchCount surveys");
            $this->processBatch($batch, $questions, $questionsMap);
            $page++;
            $mightHaveMore = $batch->count() > 0;
        } while ($mightHaveMore);

        ReportService::saveFileStream($this->report, $fileName);
        ReportService::saveMetaFile($this->report, $this->metaRows);
        ReportService::saveDataFile($this->report, $this->otherHeaders, $this->otherRows, 'other');
        ReportService::saveDataFile($this->report, $this->notesHeaders, $this->notesRows, 'notes');
        ReportService::saveImagesFile($this->report, $this->images);

    }

    private function makeHeaders ($questions) {
        $this->defaultColumns = [
            'id' => 'survey_id',
            'respondent_id' => 'respondent_master_id',
            'created_at' => 'created_at',
            'completed_at' => 'completed_at'
        ];

        $headers = [];

        $expandMultiSelect = function ($baseKey, $baseName, $choices) use (&$headers) {
            foreach ($choices as $choice) {
                $key = $baseKey . '_' . $choice->id;
                $headers[$key] = $baseName . '_' . $choice->val;
            }
        };
        $assignQuestionHeaders = function ($baseKey, $baseName, $question) use (&$headers, $expandMultiSelect) {
            if ($question->questionType->name === 'multiple_select') {
                $expandMultiSelect($baseKey, $baseName, $question->choices);
            } else {
                $headers[$baseKey] = $baseName;
            }
        };
        foreach ($questions as $question) {
            $baseKey = $question->id;
            $baseName = $question->var_name;
            if (isset($question->follow_up_question_id)) {
                $q = Datum::whereIn('question_datum_id', function ($q) use ($question) {
                    $q->select('id')
                        ->from('question_datum')
                        ->where('question_datum.question_id', '=', $question->follow_up_question_id);
                })->select('sort_order')
                ->distinct();
                $repetitions = $q->get();
                $repetitions = $repetitions->count();
                if ($repetitions > 0) {
                    for ($i = 0; $i < $repetitions; $i++) {
                        $assignQuestionHeaders($baseKey . '_r' . $i, $baseName . '_r' . ($i + 1), $question);
                    }
                } else {
                    $assignQuestionHeaders($baseKey, $baseName, $question);
                }

            } else {
                $assignQuestionHeaders($baseKey, $baseName, $question);
            }
        }

        // Sort non default columns first then add default columns
        asort($headers);
        $this->headers = $this->defaultColumns + $headers; // add at the beginning of the array
        $headersCount = count($headers);
        Log::info("$headersCount headers found");
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

    private function processBatch ($surveys, $questions, $questionsMap) {

        $rows = [];
        foreach ($surveys as $survey) {
            $row = $this->formatSurveyData($survey, $questions, $questionsMap);
            foreach($this->defaultColumns as $key=>$name){
                $row[$key] = $survey->$key;
            }
            array_push($rows, $row);
        }

        // Implode any cells that are represented as an array
        foreach ($rows as &$row) {
            foreach ($row as $key => $cell) {
                if (is_array($cell)) {
                    $row[$key] = implode(';', $cell);
                }
            }
        }

        // Write the batch to file
        $this->file->writeRows($rows);
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
        $questionDatum = $questionDatum->sort(function (QuestionDatum $a,  QuestionDatum $b) use ($questionOrderMap, $datumMap) {
            if ($a->question_id === $b->question_id) {
                if (isset($a->follow_up_datum_id) && isset($b->follow_up_datum_id)) {
                    Log::info('follow up question found '. $a->id);
                    return $datumMap[$a->follow_up_datum_id]->sort_order - $datumMap[$b->follow_up_datum_id]->sort_order;
                } else {
                    return $a->section_repetition - $b->section_repetition;
                }
            } else {
                return $questionOrderMap[$a->question_id] - $questionOrderMap[$b->question_id];
            }
        });

        foreach ($questionDatum as $qd) {
            $question = $questionsMap[$qd->question_id];
            $baseKey = $question->id; // TODO: Add repetitions
            if ($question->has_follow_up) {
                $datum = $datumMap[$qd->follow_up_datum_id];
                $baseKey .= '_r' . $datum->sort_order;
            }
            switch ($question->questionType->name) {
                case 'multiple_select':
                    // Make all of the choices their own column
                    foreach ($question->choices as $choice) {
                        $key = $baseKey . '_' . $choice->id;
                        if (!isset($this->headers[$key])) {
                            throw new Exception("Header $key should already be defined");
                        }
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
                        if (!isset($this->headers[$key])) {
                            throw new Exception("Header $key should already be defined");
                        }
                        // TODO: Handle showing crosswalk as true or false instead of values
                        if (isset($qd->dk_rf)) {
                            $row[$key] = $qd->dk_rf ? 'DK' : 'RF';
                        } else {
                            $row[$key] = $this->translationToText($datum->choice->choiceTranslation, $this->config->locale);
                        }

                        // This seems like the safest way to check if it's an other response
                        if (isset($datum->val) && strlen($datum->val) > 0 && isset($datum->choice_id) && $datum->val !== $datum->choice_id) {
                            $this->addOther($this->headers[$key], $survey->id, $survey->respondent_id, $datum->val);
                        }
                    }

                    if (isset($qd->dk_rf)) {
                        $this->addNote($this->headers[$baseKey], $survey, $qd->dk_rf, $qd->dk_rf_val);
                    }

                    break;
                default:
                    $key = $baseKey;
                    if (!isset($this->headers[$key])) {
                        throw new Exception("Header $key should already be defined");
                    }
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

}
