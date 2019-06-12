<?php

namespace App\Jobs;


use App\Classes\CsvFileStream;
use App\Models\Action;
use App\Services\ReportService;
use Illuminate\Support\Facades\Schema;
use Log;
use App\Models\Report;
use App\Models\Datum;
use App\Models\Study;
use Ramsey\Uuid\Uuid;


class ActionReportJob extends Job
{

    protected $studyId;
    protected $report;
    private $file;

    public function __construct($studyId, $fileId)
    {
        Log::debug("ActionReportJob - constructing: $studyId");
        $this->studyId = $studyId;
        $this->report = new Report();
        $this->report->id = $fileId;
        $this->report->type = 'action';
        $this->report->status = 'queued';
        $this->report->report_id = $this->studyId;
        $this->report->save();
    }

    public function handle()
    {
        set_time_limit(60 * 15);
        $startTime = microtime(true);
        Log::debug("ActionReportJob - handling: $this->studyId, $this->report->id");
        try{
            $this->create();
            $this->report->status = 'saved';
        } catch(Exception $e){
            $this->report->status = 'failed';
            $duration = microtime(true) - $startTime;
            Log::debug("ActionReportJob - failed: $this->studyId after $duration seconds");
        } finally{
            $this->report->save();
            if (isset($this->file)) {
                $this->file->close();
            }
            $duration = microtime(true) - $startTime;
            Log::debug("ActionReportJob - finished: $this->studyId in $duration seconds");
        }
    }


    public function create(){

        $columns = Schema::getColumnListing('action');
        $this->headers = array_reduce($columns, function ($agg, $col) {
            if ($col !== 'random_sort_order') {
                $agg[$col] = $col;
            }
            return $agg;
        }, []);

        $id = Uuid::uuid4();
        $fileName = $id . '.csv';
        $filePath = storage_path('app/' . $fileName);
        $this->file = new CsvFileStream($filePath, $this->headers);
        $this->file->open();
        $this->file->writeHeader();

        $q = Action::join('interview', 'interview.id', '=', 'action.interview_id')
            ->join('survey', 'interview.survey_id', '=', 'survey.id')
            ->where('survey.study_id', '=', $this->studyId)
            ->select('action.*', 'interview.survey_id');

        foreach ($q->cursor() as $action) {
            $actionRow = $action->toArray();
            $this->file->writeRow($actionRow);
        }
        ReportService::saveFileStream($this->report, $fileName);

    }
}