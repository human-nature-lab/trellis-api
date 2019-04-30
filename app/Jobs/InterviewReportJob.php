<?php

namespace App\Jobs;


use App\Classes\CsvFileWriter;
use App\Services\ReportService;
use Illuminate\Support\Facades\DB;
use Log;
use App\Models\Report;
use App\Models\Interview;
use App\Models\Study;
use Ramsey\Uuid\Uuid;


class InterviewReportJob extends Job
{

    protected $studyId;
    public $report;
    private $file;
    private $headers;
    private $defaultHeaders;

    /**
     * Create a new job instance.
     *
     * @param  $studyId
     * @return void
     */
    public function __construct($studyId, $config)
    {
        Log::debug("InterviewReportJob - constructing: $studyId");
        $this->studyId = $studyId;
        $this->report = new Report();
        $this->report->id = Uuid::uuid4();
        $this->report->type = 'interview';
        $this->report->status = 'queued';
        $this->report->study_id = $this->studyId;
        $this->report->save();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle () {
        set_time_limit(300);
        $startTime = microtime(true);
        Log::debug("InterviewReportJob - handling: $this->studyId, $this->report->id");
        try{
            $this->create();
            $this->report->status = 'saved';
        } catch(Exception $e){
            $this->report->status = 'failed';
            $duration = microtime(true) - $startTime;
            Log::error($e);
            Log::debug("InterviewReportJob - failed: $this->studyId after $duration seconds");
        } finally{
            $this->report->save();
            if (isset($this->file)) {
                $this->file->close();
            }
            $duration = microtime(true) - $startTime;
            Log::debug("InterviewReportJob - finished: $this->studyId in $duration seconds");
        }
    }


    public function create(){

        $this->makeHeaders();

        $id = Uuid::uuid4();
        $fileName = $id . '.csv';
        $filePath = storage_path('app/' . $fileName);
        $this->file = new CsvFileWriter($filePath, $this->headers);
        $this->file->open();
        $this->file->writeHeader();

        $study = Study::find($this->studyId);

        $q = Interview::join('survey', 'survey.id', '=', 'interview.survey_id')
            ->join('form', 'form.id', '=', 'survey.form_id')
            ->join('translation_text', function($join) use ($study){
                $join->on('translation_text.translation_id', '=', 'form.name_translation_id');
                $join->on('translation_text.locale_id', '=', DB::raw("'$study->default_locale_id'"));
            })
            ->leftJoin('user', 'user.id', '=', 'interview.user_id')
            ->where('survey.study_id', '=', $this->studyId)
            ->select('interview.*', 'survey.respondent_id', 'survey.form_id', 'user.name as user_name', 'user.username', 'translation_text.translated_text as form_name')
            ->addSelect(DB::raw("(select count(*) from question_datum qd where qd.survey_id = survey.id and qd.dk_rf = true) as dk_count"))
            ->addSelect(DB::raw("(select count(*) from question_datum qd where qd.survey_id = survey.id and qd.dk_rf = false) as rf_count"))
            ->orderBy('interview.created_at', 'asc');

        $q->chunk(500, function ($interviews) {
            $this->file->writeRows($interviews);
        });

        ReportService::saveFileStream($this->report, $fileName);
        // TODO: create zip file with location images

    }

    private function makeHeaders () {
        $this->headers =[
            'id' => 'interview_id',
            'survey_id' => "survey_id",
            'respondent_id' => "respondent_id",
            'form_id' => "form_id",
            'form_name' => "form_name",
            'user_id' => 'user_id',
            'user_name' => "user_name",
            'username' => 'user_username',
            'latitude' => 'latitude',
            'longitude' => 'longitude',
            'altitude' => 'altitude',
            'start_time' => 'start_time',
            'end_time' => "end_time",
            'created_at' => 'created_at',
            'updated_at' => "updated_at",
            "deleted_at" => 'deleted_at',
            'dk_count' => 'dk_count',
            'rf_count' => 'rf_count'
        ];
    }
}