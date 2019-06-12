<?php

namespace App\Jobs;

use App\Classes\CsvFileStream;
use App\Models\ConditionTag;
use App\Models\Respondent;
use App\Models\RespondentConditionTag;
use App\Services\ReportService;
use Log;
use Illuminate\Support\Facades\DB;
use App\Models\Report;
use Ramsey\Uuid\Uuid;

class RespondentReportJob extends Job
{

    protected $studyId;
    protected $report;
    protected $config;
    protected $maxGeoDepth=4;
    private $file;
    private $headers;
    private $defaultHeaders;
    private $localeId;

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
        set_time_limit(600);
        $startTime = microtime(true);
//        $this->localeId = ReportService::extractLocaleId($this->config, $this->studyId);
        $this->localeId = "48984fbe-84d4-11e5-ba05-0800279114ca";
        Log::debug("RespondentReportJob - handling: $this->studyId, $this->report->id");
        try{
//            ReportService::createRespondentReport($this->studyId, $this->report->id);
            $this->create();
            $this->report->status = 'saved';
        } catch(Exception $e){
            $this->report->status = 'failed';
            Log::error($e);
            Log::debug("RespondentReportJob:  $this->studyId failed");
        } finally{
            $this->report->save();
            if (isset($this->file)) {
                $this->file->close();
            }
            $duration = microtime(true) - $startTime;
            Log::debug("RespondentReportJob - finished: $this->studyId in $duration seconds");
        }
    }


    public function create () {

      $this->makeHeaders();

      $id = Uuid::uuid4();
      $fileName = $id . '.csv';
      $filePath = storage_path('app/' . $fileName);
      $this->file = new CsvFileStream($filePath, $this->headers);
      $this->file->open();
      $this->file->writeHeader();

        // Streaming loop
      $q = Respondent::with('currentGeo', 'currentGeo.geo.nameTranslation', 'currentGeo.geo.geoType');
      $q->chunk(400, function ($respondents) {
        $this->processBatch($respondents);
      });

      ReportService::saveFileStream($this->report, $fileName);
      // TODO: Save respondent photos as zip file

    }

    private function makeHeaders () {
        $this->defaultHeaders = [
            'id' => "respondent_id",
            'name' => "respondent_name",
            "associated_respondent_id" => "associated_respondent_id",
            'created_at' => "created_at",
            'updated_at' => "updated_at"
        ];

        $geoHeaders = [
            "current_geo_id" => "current_geo_id",
            "current_geo_name" => "current_geo_name",
            "current_geo_type" => "current_geo_type"
        ];

        $uniqueRCTQuery = ConditionTag::select('condition_tag.name')
            ->join('respondent_condition_tag', 'respondent_condition_tag.condition_tag_id', '=', 'condition_tag.id')
            ->distinct();

        Log::info($uniqueRCTQuery->toSql());

        $uniqueConditionTags = $uniqueRCTQuery->get();

        $headers = [];
        foreach ($uniqueConditionTags as $ct) {
            $headers[$ct->name] = $ct->name;
        }

        asort($headers);
        $this->headers = $this->defaultHeaders + $headers + $geoHeaders;
    }

    private function processBatch ($respondents) {
        $rows = [];
        $respondentIds = [];
        foreach ($respondents as $r) {
            array_push($respondentIds, $r->id);
        }

        $rcts = RespondentConditionTag::whereIn('respondent_id', $respondentIds)
            ->with('conditionTag')
            ->get();

        $rctMap = [];
        foreach($rcts as $rct){
            if(!isset($rctMap[$rct->respondent_id])){
                $rctMap[$rct->respondent_id] = [];
            }
            if (isset($rct->conditionTag)) {
                array_push($rctMap[$rct->respondent_id], $rct->conditionTag->name);
            }
        }

        foreach ($respondents as $respondent) {
            $row = [];

            // Apply default headers
            foreach ($this->defaultHeaders as $key => $name) {
                $row[$key] = $respondent->$key;
            }

            // Apply geo headers
            if (isset($respondent->currentGeo)) {
                $row['current_geo_id'] = $respondent->currentGeo->geo_id;
                if (isset($respondent->currentGeo->geo)) {
                    $row['current_geo_name'] = ReportService::translationToText($respondent->currentGeo->geo->nameTranslation, $this->localeId);
                    if (isset($respondent->currentGeo->geo->geoType)) {
                        $row['current_geo_type'] = $respondent->currentGeo->geo->geoType->name;
                    }
                }
            }

            // Apply all condition tags for this respondent
            if (isset($rctMap[$respondent->id])) {
                foreach ($rctMap[$respondent->id] as $tagName) {
                    if (!isset($this->headers[$tagName])) {
                        throw new \ErrorException("Invalid tag name $tagName. Tag name not already found in headers.");
                    }
                    $row[$tagName] = true;
                }
            }

            array_push($rows, $row);
        }

        $this->file->writeRows($rows);

    }

}
