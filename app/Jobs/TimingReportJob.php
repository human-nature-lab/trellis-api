<?php

namespace App\Jobs;


use App\Services\ReportService;
use Log;
use App\Models\Report;
use App\Models\Datum;
use App\Models\Study;


class TimingReportJob extends Job
{
//    use InteractsWithQueue, SerializesModels;

    protected $studyId;
    protected $report;

    public function __construct($studyId, $fileId)
    {
        Log::debug("TimingReportJob - constructing: $studyId");
        $this->studyId = $studyId;
        $this->report = new Report();
        $this->report->id = $fileId;
        $this->report->type = 'timing';
        $this->report->status = 'queued';
        $this->report->report_id = $this->studyId;
        $this->report->save();
    }

    public function handle()
    {
        $startTime = microtime(true);
        Log::debug("TimingReportJob - handling: $this->studyId, $this->report->id");
        try{
            $this->create();
            $this->report->status = 'saved';
        } catch(Exception $e){
            $this->report->status = 'failed';
            $duration = microtime(true) - $startTime;
            Log::debug("TimingReportJob - failed: $this->studyId after $duration seconds");
        } finally{
            $this->report->save();
            $duration = microtime(true) - $startTime;
            Log::debug("TimingReportJob - finished: $this->studyId in $duration seconds");
        }
    }


    public function create(){

        $study = Study::find($this->studyId);

        $datum = Datum::join('survey', 'survey.id', '=', 'datum.survey_id')
            ->join('interview', 'interview.survey_id', '=', 'survey.id')
            ->join('user', 'interview.user_id', '=', 'user.id')
            ->join('question', 'question.id', '=', 'datum.question_id')
            ->join('question_type', 'question.question_type_id', '=', 'question_type.id')
            ->where('survey.study_id', '=', $study->id)
            ->orderBy('datum.created_at', 'asc')
            ->select(
                "interview.id as interview_id",
                "datum.survey_id",
                'datum.question_id',
                "survey.form_id",
                "survey.respondent_id",
                "interview.user_id",
                "user.name",
                "user.username",
                "datum.val",
                "datum.name as question_name",
                "question_type.name as question_type",
                'datum.created_at',
                'datum.updated_at',
                'datum.deleted_at');

        Log::debug($datum->toSql());

        $headers =[
            'interview_id' => "interview_id",
            'survey_id' => "survey_id",
            'respondent_id' => 'respondent_id',
            'question_id' => 'question_id',
            'form_id' => "form_id",
            'user_id' => "user_id",
            'name' => "user_name",
            'username' => "user_username",
            'val' => "datum_value",
            'question_type' => "question_type",
            'question_name' => "question_name",
            'created_at' => 'created_at',
            'updated_at' => "updated_at",
            "deleted_at" => 'deleted_at'
        ];

        // Sort by num parents from low to high
        $rows = [];
        foreach($datum->get() as $row){
            array_push($rows, $row->toArray());
        }

        ReportService::saveDataFile($this->report, $headers, $rows);
        // TODO: create zip file with location images

    }
}