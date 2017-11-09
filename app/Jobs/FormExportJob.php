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

class FormExportJob extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $formId;
    protected $export;

    /**
     * Create a new job instance.
     *
     * @param  $formId
     * @return void
     */
    public function __construct($formId, $fileId)
    {
        Log::debug("FormExportJob - constructing: $formId");
        $this->formId = $formId;
        $this->export = new Export();
        $this->export->id = $fileId;
        $this->export->type = 'form';
        $this->export->status = 'queued';
        $this->export->export_id = $this->formId;
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
        Log::debug("FormExportJob - handling: $this->formId, $this->export->id");
        try{
            ExportService::createFormExport($this->formId, $this->export->id);
            $this->export->status = 'saved';
        } catch(Exception $e){
            $this->export->status = 'failed';
            Log::debug("Form export $this->formId failed");
            throw $e;
        }
        $this->export->save();
        $duration = microtime(true) - $startTime;
        Log::debug("FormExportJob - finished: $this->formId in $duration seconds");

    }
}