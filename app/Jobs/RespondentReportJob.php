<?php

namespace App\Jobs;

use Log;
use App\Models\Report;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\ReportService;
use Ramsey\Uuid\Uuid;

class RespondentReportJob extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $studyId;
    protected $report;
    protected $config;

    /**
     * Create a new job instance.
     *
     * @param  $formId
     * @return void
     */
    public function __construct($studyId, $fileId, $config)
    {
        Log::debug("RespondentReportJob - constructing: $studyId");
        $this->config = $config;
        $this->studyId = $studyId;
        $this->report = new Report();
        $this->report->id = $fileId;
        $this->report->type = 'respondent';
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
        Log::debug("RespondentReportJob - handling: $this->studyId, $this->report->id");
        try{
            ReportService::createRespondentReport($this->studyId, $this->report->id);
            $this->report->status = 'saved';
        } catch(Exception $e){
            $this->report->status = 'failed';
            Log::debug("RespondentReportJob:  $this->studyId failed");
            throw $e;
        }
        $this->report->save();
        $duration = microtime(true) - $startTime;
        Log::debug("RespondentReportJob - finished: $this->studyId in $duration seconds");
    }
}