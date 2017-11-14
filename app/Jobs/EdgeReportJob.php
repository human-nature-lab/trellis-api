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

class EdgeReportJob extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $studyId;
    protected $export;

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
        $this->export = new Report();
        $this->export->id = $fileId;
        $this->export->type = 'edge';
        $this->export->status = 'queued';
        $this->export->report_id = $this->studyId;
        $this->export->save();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $startTime = microtime(true);
        Log::debug("EdgeReportJob - handling: $this->studyId, $this->export->id");
        try{
            ReportService::createEdgesReport($this->studyId, $this->export->id);
            $this->export->status = 'saved';
        } catch(Exception $e){
            $this->export->status = 'failed';
            Log::debug("Edge report $this->studyId failed");
            throw $e;
        }
        $this->export->save();
        $duration = microtime(true) - $startTime;
        Log::debug("EdgeReportJob - finished: $this->studyId in $duration seconds");
    }
}