<?php

namespace App\Jobs;

use Log;
use App\Models\Export;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\ExportService;
use Ramsey\Uuid\Uuid;

class RespondentExportJob extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $studyId;
    protected $export;

    /**
     * Create a new job instance.
     *
     * @param  $formId
     * @return void
     */
    public function __construct($studyId, $fileId)
    {
        Log::debug("StudyExportJob - constructing: $studyId");
        $this->studyId = $studyId;
        $this->export = new Export();
        $this->export->id = $fileId;
        $this->export->type = 'respondent';
        $this->export->status = 'queued';
        $this->export->export_id = $this->studyId;
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
        Log::debug("RespondentExportJob - handling: $this->studyId, $this->export->id");
        try{
            ExportService::createRespondentExport($this->studyId, $this->export->id);
            $this->export->status = 'saved';
        } catch(Exception $e){
            $this->export->status = 'failed';
            Log::debug("RespondentExportJob:  $this->studyId failed");
            throw $e;
        }
        $this->export->save();
        $duration = microtime(true) - $startTime;
        Log::debug("RespondentExportJob - finished: $this->studyId in $duration seconds");
    }
}