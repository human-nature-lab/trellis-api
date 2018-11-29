<?php

namespace App\Jobs;

use App\Classes\CsvFileStream;
use App\Models\Edge;
use App\Models\RespondentGeo;
use App\Services\ReportService;
use Log;
use App\Models\Report;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class RespondentGeoJob extends Job
{

    protected $studyId;
    protected $report;
    private $headers;
    private $file;

    /**
     * Create a new job instance.
     *
     * @param  $studyId
     * @return void
     */
    public function __construct($studyId, $fileId)
    {
        Log::debug("RespondentGeoReport - constructing: $studyId");
        $this->studyId = $studyId;
        $this->report = new Report();
        $this->report->id = $fileId;
        $this->report->type = 'respondent_geo';
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
        Log::debug("RespondentGeoReport - handling: $this->studyId, $this->report->id");
        try{
            $this->create();
            $this->report->status = 'saved';
        } catch(Exception $e){
            $this->report->status = 'failed';
            Log::debug("Edge report $this->studyId failed");
        } finally{
            $this->report->save();
            if (isset($this->file)) {
                $this->file->close();
            }
            $duration = microtime(true) - $startTime;
            Log::debug("RespondentGeoReport - finished: $this->studyId in $duration seconds");
        }
    }


    public function create(){

        $this->makeHeaders();

        $id = Uuid::uuid4();
        $fileName = $id . '.csv';
        $filePath = storage_path('app/' . $fileName);
        $this->file = new CsvFileStream($filePath, $this->headers);
        $this->file->open();
        $this->file->writeHeader();

        RespondentGeo::chunk(1000, function ($rGeos) {
            $rGeos = $rGeos->toArray();
            $this->file->writeRows($rGeos);
        });
        ReportService::saveFileStream($this->report, $fileName);
        // TODO: create zip file with location images

    }

    private function makeHeaders () {
        $this->headers = [
            'id' => 'id',
            'respondent_id' => 'respondent_id',
            'geo_id' => 'geo_id',
            'previous_respondent_geo_id' => 'previous_respondent_geo_id',
            'is_current' => 'is_current',
            'notes' => 'notes',
            'updated_at' => 'updated_at',
            'created_at' => 'created_at'
        ];
    }
}