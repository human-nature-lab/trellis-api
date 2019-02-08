<?php

namespace App\Jobs;

use App\Classes\CsvFileStream;
use App\Models\Edge;
use App\Services\ReportService;
use Log;
use App\Models\Report;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class EdgeReportJob extends Job
{

    protected $studyId;
    protected $report;
    private $headers;
    private $file;

    /**
     * Create a new job instance.
     *
     * @param  $studyId
     * @return void
     */
    public function __construct($studyId, $fileId)
    {
        Log::debug("EdgeReportJob - constructing: $studyId");
        $this->studyId = $studyId;
        $this->report = new Report();
        $this->report->id = $fileId;
        $this->report->type = 'edge';
        $this->report->status = 'queued';
        $this->report->report_id = $this->studyId;
        $this->report->save();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $startTime = microtime(true);
        Log::debug("EdgeReportJob - handling: $this->studyId, $this->report->id");
        try{
//            ReportService::createEdgesReport($this->studyId, $this->export->id);
            $this->create();
            $this->report->status = 'saved';
        } catch(Exception $e){
            $this->report->status = 'failed';
            Log::debug("Edge report $this->studyId failed");
        } finally{
            $this->report->save();
            if (isset($this->file)) {
                $this->file->close();
            }
            $duration = microtime(true) - $startTime;
            Log::debug("EdgeReportJob - finished: $this->studyId in $duration seconds");
        }
    }


    public function create(){

        $this->makeHeaders();

        $id = Uuid::uuid4();
        $fileName = $id . '.csv';
        $filePath = storage_path('app/' . $fileName);
        $this->file = new CsvFileStream($filePath, $this->headers);
        $this->file->open();
        $this->file->writeHeader();

        Edge::leftJoin('respondent as sourceR', 'sourceR.id', '=', 'edge.source_respondent_id')
            ->leftJoin('respondent as targetR', 'targetR.id', '=', 'edge.target_respondent_id')
            ->leftJoin('datum', 'datum.edge_id', '=', 'edge.id')
            ->leftJoin('question_datum', 'datum.question_datum_id', '=', 'question_datum.id')
            ->leftJoin('question', 'question.id', '=', 'question_datum.question_id')
            ->leftJoin('survey', 'survey.id', '=', 'question_datum.survey_id')
            ->where('survey.study_id', '=', $this->studyId)
            ->select(
                'edge.id',
                'sourceR.id as sId',
                'targetR.id as tId',
                'sourceR.name as sName',
                'targetR.name as tName',
                'question.var_name',
                'survey.updated_at',
                'question_datum.dk_rf',
                'question_datum.dk_rf_val',
                'question_datum.survey_id'
            )->chunk(200, function ($edges) {
                $e = $edges->map(function ($m) {
                    $dk = 'dk_rf';
                    if (!is_null($m[$dk])) {
                        $m[$dk] = $m[$dk] === 1 ? 'Dont_know': 'Refused';
                    }
                    return $m;
                });
                $this->file->writeRows($e);
            });
        ReportService::saveFileStream($this->report, $fileName);
        // TODO: create zip file with location images

    }

    private function makeHeaders () {
        $this->headers = [
            'id' => 'id',
            'sId' => 'ego',
            'sName' => 'ego_name',
            'tId' => 'alter',
            'tName' => 'alter_name',
            'var_name' => 'question',
            'updated_at' => 'survey_updated_at',
            'dk_rf' => "question_dk_rf",
            'dk_rf_val' => "question_dk_rf_response",
            'survey_id' => 'survey_id'
        ];
    }
}