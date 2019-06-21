<?php

namespace App\Jobs;

use App\Classes\CsvFileStream;
use App\Models\Edge;
use App\Models\QuestionDatum;
use App\Services\ReportService;
use Log;
use App\Models\Report;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class EdgeReportJob extends Job
{

    protected $studyId;
    public $report;
    private $headers;
    private $file;

    /**
     * Create a new job instance.
     *
     * @param  $studyId
     * @return void
     */
    public function __construct($studyId, $config)
    {
        Log::debug("EdgeReportJob - constructing: $studyId");
        $this->studyId = $studyId;
        $this->report = new Report();
        $this->report->id = Uuid::uuid4();
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
    public function handle () {

        set_time_limit(0);
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

        $q = DB::table('question_datum')->whereIn('question_type_id', function ($s) {
            return $s->select('id')->from('question_type')->where('name', '=', DB::raw('"relationship"'));
        })
            ->leftJoin('datum', 'datum.question_datum_id', '=', 'question_datum.id')
            ->leftJoin('edge', 'datum.edge_id', '=', 'edge.id')
            ->leftJoin('respondent as sourceR', 'edge.source_respondent_id', '=', 'sourceR.id')
            ->leftJoin('respondent as targetR', 'edge.target_respondent_id', '=', 'targetR.id')
            ->join('question', 'question.id', '=', 'question_datum.question_id')
            ->join('survey', 'survey.id', '=', 'question_datum.survey_id')
            ->where('survey.study_id', '=', $this->studyId)
            ->select(
                'edge.id',
                'sourceR.id as sId',
                'targetR.id as tId',
                'sourceR.name as sName',
                'targetR.name as tName',
                'question.var_name',
                'question_datum.updated_at',
                'question_datum.dk_rf',
                'question_datum.dk_rf_val',
                'question_datum.no_one',
                'question.id as qId',
                'question_datum.survey_id'
            );
        foreach ($q->cursor() as $edge) {
          $edge = json_decode(json_encode($edge), true);
          $dk = 'dk_rf';
          if (!is_null($edge[$dk])) {
              $edge[$dk] = $edge[$dk] === 1 ? 'Dont_know': 'Refused';
          }
          $this->file->writeRow($edge);
        }
//        $q->chunk(1000, function ($edges) {
//          foreach ($edges as $edge) {
//            $dk = 'dk_rf';
//            if (!is_null($edge[$dk])) {
//              $edge[$dk] = $edge[$dk] === 1 ? 'Dont_know': 'Refused';
//            }
//            $this->file->writeRow($edge);
//          }
//        });
        ReportService::saveFileStream($this->report, $fileName);
        // TODO: create zip file with location images

    }

    private function makeHeaders () {
        $this->headers = [
            'var_name' => 'question',
            'survey_id' => 'survey_id',
            'dk_rf' => "question_dk_rf",
            'dk_rf_val' => "question_dk_rf_response",
            'no_one' => 'question_no_one',
            'id' => 'edge_id',
            'sId' => 'ego_id',
            'sName' => 'ego_name',
            'tId' => 'alter_id',
            'tName' => 'alter_name',
            'updated_at' => 'survey_updated_at',
            'qId' => 'question_id',
        ];
    }
}
