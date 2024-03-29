<?php

namespace App\Jobs;


use App\Library\CsvFileWriter;
use App\Models\Interview;
use App\Models\QuestionType;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Support\Facades\DB;
use Log;
use App\Models\Report;
use App\Models\Study;
use Ramsey\Uuid\Uuid;


class TimingReportJob extends Job
{

    protected $studyId;
    public $report;
    protected $headers;
    private $file;

    public function __construct($studyId, $config)
    {
        Log::debug("TimingReportJob - constructing: $studyId");
        $this->studyId = $studyId;
        $this->report = new Report();
        $this->report->id = Uuid::uuid4();
        $this->report->type = 'timing';
        $this->report->status = 'queued';
        $this->report->study_id = $this->studyId;
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
          'interview_id' => 'interview_id',
          'survey_id' => 'survey_id',
          'respondent_id' => 'respondent_id',
          'question_id' => 'question_id',
          'form_id' => 'form_id',
          'user_id' => 'user_id',
          'name' => 'user_name',
          'username' => 'user_username',
          'question_type' => 'question_type',
          'question_name' => 'question_name',
          'created_at' => 'created_at',
          'updated_at' => 'updated_at',
          'deleted_at' => 'deleted_at',
          'last_response_time' => 'last_response_time'
        ];

        $id = Uuid::uuid4();
        $fileName = $id . '.csv';
        $filePath = storage_path('app/' . $fileName);
        $this->file = new CsvFileWriter($filePath, $this->headers);
        $this->file->open();
        $this->file->writeHeader();

        $study = Study::find($this->studyId);

        $questionTypes = QuestionType::all()->reduce(function ($agg, $qt) {
            $agg[$qt->id] = $qt->name;
            return $agg;
        }, []);

        $users = User::withTrashed()->get()->reduce(function ($agg, $user) {
            $agg[$user->id] = $user;
            return $agg;
        }, []);

        $batchSize = (int)env('TIMING_REPORT_BATCH_SIZE', 200);
        $interviews = Interview::join('survey', 'interview.survey_id', '=', 'survey.id')
            ->where('survey.study_id', '=', $study->id);
        $interviews->chunk($batchSize, function ($interviews) use ($users, $questionTypes) {
            $interviewToSurveyMap = [];
            $interviewMap = [];
            $surveyIds = [];
            foreach ($interviews as $interview) {
                array_push($surveyIds, $interview->survey_id);
                $interviewToSurveyMap[$interview->id] = $interview->survey_id;
                $interviewMap[$interview->survey_id] = $interview;
            }
            $questionData = DB::table('question_datum')
                ->join('question', 'question_datum.question_id', '=', 'question.id')
                ->whereIn('survey_id', $surveyIds)
                ->whereNull('question_datum.deleted_at')
                ->whereNull('question.deleted_at')
                ->select(
                    'question.var_name',
                    'question.question_type_id',
                    'question_id',
                    'question_datum.survey_id',
                    'question_datum.created_at',
                    'question_datum.updated_at',
                    'question_datum.deleted_at',
                    DB::raw('(select updated_at from datum where datum.question_datum_id = question_datum.id and datum.deleted_at is null order by updated_at desc limit 1) last_response_time')
                )->get();
            foreach ($questionData as $qd) {
                $interview = $interviewMap[$qd->survey_id];
                $user = $users[$interview->user_id];
                $questionTypeName = $questionTypes[$qd->question_type_id];
                $this->file->writeRow([
                    'interview_id' => $interview->id,
                    'survey_id' => $interview->survey_id,
                    'question_id' => $qd->question_id,
                    'form_id' => $interview->form_id,
                    'respondent_id' => $interview->respondent_id,
                    'study_id' => $interview->study_id,
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'question_name' => $qd->var_name,
                    'question_type' => $questionTypeName,
                    'created_at' => $qd->created_at,
                    'updated_at' => $qd->updated_at,
                    'deleted_at' => $qd->deleted_at,
                    'last_response_time' => $qd->last_response_time
                ]);
            }
        });

        ReportService::saveFileStream($this->report, $fileName);
        // TODO: create zip file with location images

    }
}
