<?php

namespace App\Jobs;


use App\Services\ReportService;
use Illuminate\Support\Facades\DB;
use Log;
use App\Models\Report;
use App\Models\Interview;
use App\Models\Study;


class InterviewReportJob extends Job
{
//    use InteractsWithQueue, SerializesModels;

    protected $studyId;
    protected $report;

    /**
     * Create a new job instance.
     *
     * @param  $studyId
     * @return void
     */
    public function __construct($studyId, $fileId)
    {
        Log::debug("InterviewReportJob - constructing: $studyId");
        $this->studyId = $studyId;
        $this->report = new Report();
        $this->report->id = $fileId;
        $this->report->type = 'interview';
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
        Log::debug("InterviewReportJob - handling: $this->studyId, $this->report->id");
        try{
            $this->create();
            $this->report->status = 'saved';
        } catch(Exception $e){
            $this->report->status = 'failed';
            $duration = microtime(true) - $startTime;
            Log::debug("InterviewReportJob - failed: $this->studyId after $duration seconds");
        } finally{
            $this->report->save();
            $duration = microtime(true) - $startTime;
            Log::debug("InterviewReportJob - finished: $this->studyId in $duration seconds");
        }
    }


    public function create(){

        $study = Study::find($this->studyId);

        $interviews = Interview::leftJoin('survey', 'survey.id', '=', 'interview.survey_id')
            ->leftJoin('form', 'form.id', '=', 'survey.form_id')
            ->leftJoin('translation_text', function($join) use ($study){
                $join->on('translation_text.translation_id', '=', 'form.name_translation_id');
                $join->on('translation_text.locale_id', '=', DB::raw("'$study->default_locale_id'"));
            })
            ->leftJoin('user', 'user.id', '=', 'interview.user_id')
            ->where('survey.study_id', '=', $this->studyId)
            ->select('interview.*', 'survey.respondent_id', 'survey.form_id', 'user.name as user_name', 'user.username', 'translation_text.translated_text as form_name');

        Log::debug($interviews->toSql());

        $headers =[
            'id' => 'Interview Id',
            'survey_id' => "Survey Id",
            'respondent_id' => "Respondent Id",
            'form_id' => "Form Id",
            'form_name' => "Form name",
            'user_id' => 'User Id',
            'user_name' => "User name",
            'username' => 'User username',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'altitude' => 'Altitude',
            'start_time' => 'Start Time',
            'end_time' => "End Time",
            'created_at' => 'Created At',
            'updated_at' => "Updated At",
            "deleted_at" => 'Deleted At'
        ];

        // Sort by num parents from low to high
        $rows = [];
        foreach($interviews->get() as $row){
            array_push($rows, $row->toArray());
        }

        ReportService::saveDataFile($this->report, $headers, $rows);
        // TODO: create zip file with location images

    }
}