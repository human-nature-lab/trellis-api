<?php

namespace App\Jobs;

use App\Classes\CsvFileStream;
use App\Models\Datum;
use App\Models\Question;
use App\Models\QuestionDatum;
use App\Models\Survey;
use App\Models\Translation;
use Illuminate\Database\Eloquent\Collection;
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
    private $localeId;
    private $file;
    private $count = 0;

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

    public function handle () {
        set_time_limit(0);
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
            Log::debug("FormReportJob - finished: $this->formId in $duration seconds. Processed $this->count surveys.");
        }
    }

    /**
     * Actually create the FormReport
     */
    private function create(){

        $this->localeId = ReportService::extractLocaleId($this->config, null);
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
            'type' => 'type',
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
            ->orderBy('form_section.sort_order')
            ->orderBy('section_question_group.question_group_order')
            ->orderBy('question.sort_order')
            ->select('question.*', 'form_section.follow_up_question_id', 'form_section.is_repeatable', 'form_section.randomize_follow_up')
            ->with('choices');

        $questions = $questions->get();

        $questionsMap = [];
        foreach ($questions as $question) {
            $question->has_follow_up = isset($question->follow_up_question_id);
            $questionsMap[$question->id] = $question;
        }

        $this->makeHeaders($questions);
        $this->makeBaseFormMetadata($questions);

        $id = Uuid::uuid4();
        $fileName = $id . '.csv';
        $filePath = storage_path('app/' . $fileName);
        $this->file = new CsvFileStream($filePath, $this->headers);
        $this->file->open();
        $this->file->writeHeader();

        $q = Survey::where('form_id', '=', $this->formId)
            ->leftJoin('respondent_geo as rg', function ($join) {
                $join->on('rg.respondent_id', '=', 'survey.respondent_id');
                $join->on('rg.is_current', '=', DB::raw('1'));
            })
            ->leftJoin('geo', 'geo.id', '=', 'rg.geo_id')
            ->leftJoin('translation_text as tt', function ($join) {
                $join->on('tt.translation_id', '=', 'geo.name_translation_id');
                $join->on('tt.locale_id', '=', DB::raw('"'.$this->localeId.'"'));
            })
            ->select(
                'survey.*',
                'rg.id as rg_id',
                'rg.geo_id as current_location_id',
                'tt.translated_text as current_location_name'
            );

        $batchSize = 400;
        $batch = new Collection;
        foreach ($q->cursor() as $survey) {
            $batch->push($survey);
            if ($batch->count() >= $batchSize) {
                $this->processBatch($batch, $questions, $questionsMap);
                $batch = new Collection;
            }
        }

        if ($batch->count() > 0) {
            $this->processBatch($batch, $questions, $questionsMap);
            $batch = null;
        }

        ReportService::saveFileStream($this->report, $fileName);
        ReportService::saveMetaFile($this->report, $this->metaRows);
        ReportService::saveDataFile($this->report, $this->otherHeaders, $this->otherRows, 'other');
        ReportService::saveDataFile($this->report, $this->notesHeaders, $this->notesRows, 'notes');
        ReportService::saveImagesFile($this->report, $this->images);

    }

    private function makeHeaders (&$questions) {
        $this->defaultColumns = [
            'id' => 'survey_id',
            'respondent_id' => 'respondent_master_id',
            'current_location_id' => 'current_location_id',
            'current_location_name' => 'current_location_name',
            'created_at' => 'created_at',
            'completed_at' => 'completed_at'
        ];

        $headers = [];

        $expandRespondentGeo = function ($baseKey, $baseName) use (&$headers) {
            $headers[$baseKey . '_ids'] = $baseName . '_ids';
            $headers[$baseKey . '_actions'] = $baseName . '_actions';
        };

        $expandMultiSelect = function ($baseKey, $baseName, $choices) use (&$headers) {
            $headers[$baseKey] = $baseName;
            foreach ($choices as $choice) {
                $key = $baseKey . '_' . $choice->id;
                $headers[$key] = $baseName . '_' . $choice->val;
            }
        };
        $assignQuestionHeaders = function ($baseKey, $baseName, $question) use (&$headers, $expandMultiSelect, $expandRespondentGeo) {
            switch ($question->questionType->name) {
                case 'multiple_select':
                    $expandMultiSelect($baseKey, $baseName, $question->choices);
                    break;
                case 'respondent_geo':
                    $expandRespondentGeo($baseKey, $baseName);
                    break;
                default:
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
                $repetitions = $repetitions === 0 ? 1 : $repetitions;
                for ($i = 0; $i < $repetitions; $i++) {
                    $assignQuestionHeaders($baseKey . '_r' . $i, $baseName . '_r' . ReportService::zeroPad($i), $question);
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

    private function getDatumValue (Datum $datum, String $localeId = null) {
        if (isset($datum->roster)) {
            return $datum->roster->val;
        } else if (isset($datum->choice)) {
            return ReportService::translationToText($datum->choice->choiceTranslation, $localeId);
        } else if (isset($datum->geo)) {
            return ReportService::translationToText($datum->geo->nameTranslation, $localeId);
        } else if (isset($datum->edge)) {
            return $datum->edge->target_respondent_id;
        } else if (isset($datum->photo)) {
            // TODO: Store photos meta data here
            return $datum->photo->file_name;
        } else if (isset($datum->respondentGeo)) {
            return ReportService::translationToText($datum->respondentGeo->geo->nameTranslation, $localeId);
        } else if (isset($datum->respondentName)) {
            return $datum->respondentName->name;
        } else {
            return $datum->val;
        }
    }

    private function processBatch ($surveys, $questions, $questionsMap) {
        $count = count($surveys);
        $this->count += $count;
        Log::debug("Processing $count surveys");
        $surveyIds = $surveys->map(function ($s) { return $s->id; });
        $batchData = QuestionDatum::whereIn('survey_id', $surveyIds)
            ->orderBy('survey_id')
            ->orderBy('created_at')
            ->with('fullData')
            ->get();

        $surveyDataMap = $batchData->reduce(function ($agg, $qd) {
            if (!isset($agg[$qd->survey_id])) {
                $agg[$qd->survey_id] = new Collection();
            }
            $agg[$qd->survey_id]->push($qd);
            return $agg;
        }, []);

        $rows = [];
        foreach ($surveys as $survey) {
            if (isset($surveyDataMap[$survey->id])) {
                $formatStart = microtime(true);
                $row = $this->formatSurveyData($survey, $questions, $questionsMap, $surveyDataMap[$survey->id]);
                $formatTime = microtime(true) - $formatStart;
            } else {
                $row = [];
            }
            foreach($this->defaultColumns as $key => $name){
                $row[$key] = $survey->$key;
            }
            // Show a different value for the Unknown location besides null
            if (!is_null($survey['rg_id']) && is_null($survey['current_location_id'])) {
                $row['current_location_name'] = 'UNKNOWN LOCATION';
                $row['current_location_id'] = 'UNKNOWN LOCATION';
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

    private function formatSurveyData ($survey, $questions, $questionsMap, $questionDatum) {
//        $questionDatum2 = QuestionDatum::where('survey_id', $survey->id)
//            ->with('fullData');
//        $questionDatum2 = $questionDatum2->get();
        $row = [];

        // Create indexes
        $questionToQuestionDatumMap = [];
        $datumMap = [];
        foreach ($questionDatum as $qd) {
            if (!isset($questionToQuestionDatumMap[$qd->question_id])) {
                $questionToQuestionDatumMap[$qd->question_id] = [];
            }
            array_push($questionToQuestionDatumMap[$qd->question_id], $qd);
            $i = 0;
            foreach ($qd->fullData as $datum) {
                $datum->repeat_index = $i;
                $datumMap[$datum->id] = $datum; // For looking up follow up info
                $i++;
            }
        }

        // Filter out questionDatum that are from deleted datum
        $questionDatum = $questionDatum->filter(function ($qd) use ($questionsMap, $datumMap) {
            if (!isset($questionsMap[$qd->question_id])) {
              return false;
            }
            $question = $questionsMap[$qd->question_id];
            $keep = isset($qd->follow_up_datum_id) || $question->has_follow_up ? isset($datumMap[$qd->follow_up_datum_id]) : true;
            return $keep;
        });

        // Index the question order
        $questionOrderMap = [];
        foreach ($questions as $index => $question) {
            $questionOrderMap[$question->id] = $index;
        }

        $datumSortStart = microtime(true);
        // Sort question datum
        foreach ($questionDatum as $qd) {
            $qd->fullData = $qd->fullData->sortBy('repeat_index');
        }
        $datumSortTime = microtime(true) - $datumSortStart;
//        Log::debug("Datum sort time $datumSortTime");

        // TODO: Make complete form test to use for exporting. Repeated sections with each question type

        $qdSortStart = microtime(true);
        // Make sure the $questionDatum are all ordered the same way
        $questionDatum = $questionDatum->sort(function (QuestionDatum $a,  QuestionDatum $b) use ($questionOrderMap, $datumMap) {
            if ($a->question_id === $b->question_id) {
                if (isset($a->follow_up_datum_id) && isset($b->follow_up_datum_id)) {
                    return $datumMap[$a->follow_up_datum_id]->repeat_index - $datumMap[$b->follow_up_datum_id]->repeat_index;
                } else {
                    return $a->section_repetition - $b->section_repetition;
                }
            } else {
                return $questionOrderMap[$a->question_id] - $questionOrderMap[$b->question_id];
            }
        });
        $qdSortTime = microtime(true) - $qdSortStart;
//        Log::debug("Question datum sort $qdSortTime");

        foreach ($questionDatum as $qd) {
            $question = $questionsMap[$qd->question_id];
            $baseKey = $question->id;
            if ($question->has_follow_up) {
                $datum = $datumMap[$qd->follow_up_datum_id];
                $baseKey .= '_r' . $datum->repeat_index;
            }
//            $this->addMetadata($baseKey, $question);
            switch ($question->questionType->name) {
                case 'multiple_select':
                    foreach ($qd->fullData as $datum) {
                        $key = $baseKey . '_' . $datum->choice->id;
                        if (!isset($this->headers[$key])) {
                            throw new Exception("Header $key should already be defined");
                        }
                        // TODO: Handle showing crosswalk as true or false instead of values
                        if (isset($qd->dk_rf)) {
                            $row[$key] = $this->mapDkRf($qd->dk_rf);
                        } else {
                            $row[$key] = ReportService::translationToText($datum->choice->choiceTranslation, $this->localeId);
                        }

                        // This seems like the safest way to check if it's an other response
                        if (isset($datum->val) && strlen($datum->val) > 0 && isset($datum->choice_id) && $datum->val !== $datum->choice->val) {
                            $this->addOther($this->headers[$key], $survey->id, $survey->respondent_id, $datum->val);
                        }
                    }

                    if (!is_null($qd->dk_rf)) {
                        $row[$baseKey] = $this->mapDkRf($qd->dk_rf);
                        $this->addNote($this->headers[$baseKey], $survey, $this->mapDkRf($qd->dk_rf), $qd->dk_rf_val);
                    }

                    break;
                case 'respondent_geo':
                    $idKey = $baseKey . '_ids';
                    $actionKey = $baseKey . '_actions';
                    if (!isset($this->headers[$idKey])) {
                        throw new Exception("Header $idKey should already be defined");
                    } else if (!isset($this->headers[$actionKey])) {
                        throw new Exception("Header $actionKey should already be defined");
                    }
                    if (!is_null($qd->dk_rf)) {
                        $row[$idKey] = $this->mapDkRf($qd->dk_rf);
                        $row[$actionKey] = $this->mapDkRf($qd->dk_rf);
                        $this->addOther($this->headers[$idKey], $survey, $qd->dk_rf, $qd->dk_rf_val);
                    } else {
                        $ids = [];
                        $actions = [];
                        foreach ($qd->fullData as $datum) {
                            array_push($ids, $datum->respondent_geo_id);
                            array_push($actions, $datum->val);
                        }
                        $row[$idKey] = $ids;
                        $row[$actionKey] = $actions;
                    }
                    break;
                case 'relationship':
                    if (isset($qd->no_one) && $qd->no_one) {
                        $row[$baseKey] = ['No_One'];
                        break;
                    }
                default:
                    $key = $baseKey;
                    if (!isset($this->headers[$key])) {
                        throw new Exception("Header $key should already be defined");
                    }
                    $vals = [];
                    if (isset($qd->dk_rf)) {
                        $dkRf = $this->mapDkRf($qd->dk_rf);
                        array_push($vals, $dkRf);
                        $this->addNote($this->headers[$key], $survey, $dkRf, $qd->dk_rf_val);
                    } else {
                        foreach ($qd->fullData as $datum) {
                            array_push($vals, $this->getDatumValue($datum, $this->localeId));
                        }
                    }
                    $row[$key] = $vals;
            }
        }
        return $row;
    }

    private function addMetadata (String $baseKey, Question $question) {
        $keys = [$baseKey];
        switch ($question->questionType->name) {
            case 'multiple_select':
                foreach ($keys as $bKey) {
                    foreach ($question->choices as $choice) {
                        $key = $bKey . '_' . $choice->id;
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
                            $lang = $tt->locale->language_name;
                            $this->metaRows[$this->headers[$key]]["option_$lang"] = $tt->translated_text;
                        }
                        foreach ($question->questionTranslation->translationText as $tt) {
                            $lang = $tt->locale->language_name;
                            $this->metaRows[$this->headers[$key]]["question_$lang"] = $tt->translated_text;
                        }
                    }
                }
                break;
            case 'respondent_geo':
                $keys = [$baseKey . '_ids', $baseKey . '_actions'];
            default:
                foreach ($keys as $key) {
                    $this->metaRows[$this->headers[$key]] = [
                        'header' => $this->headers[$key],
                        'question_type' => $question->questionType->name,
                        'variable_name' => $question->var_name
                    ];
                    foreach ($question->questionTranslation->translationText as $t) {
                        $lang = $t->locale->language_name;
                        $this->metaRows[$this->headers[$key]]["question_$lang"] = $t->translated_text;
                    }
                }
        }
    }

    private function makeBaseFormMetadata (&$questions) {
        foreach ($questions as $q) {
            if (!$q->has_follow_up) {
                $this->addMetadata($q->id, $q);
            } else {
                $this->addMetadata($q->id . '_r0', $q);
            }
        }
    }

    private function mapDkRf ($dkRf) {
        return $dkRf ? 'DK' : 'RF';
    }

    private function makeMultiSelectMeta (Question $question, String $baseKey) {
        // Make all of the choices their own column

    }

    private function addNote ($questionName, $survey, $type, $text){
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
