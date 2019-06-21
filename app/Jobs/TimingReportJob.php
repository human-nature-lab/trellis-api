<?php

namespace App\Jobs;


use App\Classes\CsvFileStream;
use App\Models\QuestionDatum;
use App\Services\ReportService;
use Log;
use App\Models\Report;
use App\Models\Datum;
use App\Models\Study;
use Ramsey\Uuid\Uuid;


class TimingReportJob extends Job
{

    protected $studyId;
    protected $report;
    protected $headers;
    private $file;

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

    public function handle () {
        set_time_limit(0);
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
            if (isset($this->file)) {
                $this->file->close();
            }
            $this->report->save();
            $duration = microtime(true) - $startTime;
            Log::debug("TimingReportJob - finished: $this->studyId in $duration seconds");
        }
    }

    public function create(){

        $this->headers = [
            'interview_id' => "interview_id",
            'survey_id' => "survey_id",
            'respondent_id' => 'respondent_id',
            'question_id' => 'question_id',
            'form_id' => "form_id",
            'user_id' => "user_id",
            'name' => "user_name",
            'username' => "user_username",
            'question_type' => "question_type",
            'question_name' => "question_name",
            'created_at' => 'created_at',
            'updated_at' => "updated_at",
            "deleted_at" => 'deleted_at'
        ];

        $id = Uuid::uuid4();
        $fileName = $id . '.csv';
        $filePath = storage_path('app/' . $fileName);
        $this->file = new CsvFileStream($filePath, $this->headers);
        $this->file->open();
        $this->file->writeHeader();

        $study = Study::find($this->studyId);

        $q = QuestionDatum::join('survey', 'survey.id', '=', 'question_datum.survey_id')
            ->join('interview', 'interview.survey_id', '=', 'survey.id')
            ->join('user', 'interview.user_id', '=', 'user.id')
            ->join('question', 'question.id', '=', 'question_datum.question_id')
            ->join('question_type', 'question.question_type_id', '=', 'question_type.id')
            ->where('survey.study_id', '=', $study->id)
            // ->orderBy('survey.respondent_id')
            ->select(
                "interview.id as interview_id",
                "question_datum.survey_id",
                'question_datum.question_id',
                "survey.form_id",
                "survey.respondent_id",
                "survey.study_id",
                "interview.user_id",
                "user.name",
                "user.username",
                "question.var_name as question_name",
                "question_type.name as question_type",
                'question_datum.created_at',
                'question_datum.updated_at',
                'question_datum.deleted_at');

        //Log::debug($q->toSql());
      
        foreach ($q->cursor() as $datum) {
            $datum = $datum->toArray();
            $this->file->writeRow($datum);
        }

        ReportService::saveFileStream($this->report, $fileName);

        // TODO: create zip file with location images

    }
}
