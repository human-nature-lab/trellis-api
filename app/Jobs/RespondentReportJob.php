<?php

namespace App\Jobs;

use App\Classes\CsvFileStream;
use App\Models\ConditionTag;
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


    public function create(){

        $this->makeHeaders();

        $id = Uuid::uuid4();
        $fileName = $id . '.csv';
        $filePath = storage_path('app/' . $fileName);
        $this->file = new CsvFileStream($filePath, $this->headers);
        $this->file->open();
        $this->file->writeHeader();

        $batchSize = 1000;
        $page = 0;

        // Streaming loop
        do {
            $offset = $batchSize * $page;
            $respondents = DB::select("select r.id, r.name as rname, r.created_at, r.updated_at, r.assigned_id,   
              (select translated_text from translation_text where translation_id in 
                (select household.name_translation_id from geo household where household.id = (
                  select rg.geo_id from respondent_geo rg where rg.respondent_id = r.id and rg.is_current = 1 limit 1
                )) 
                limit 1
              ) as household_name,
            
              (select h1.id from geo h1 where h1.id = r.geo_id) as household_id,
            
              (select translated_text from translation_text where translation_id in 
                (select building.name_translation_id from geo building where building.id in 
                  (select household.parent_id from geo household where household.id = (
                    select rg.geo_id from respondent_geo rg where rg.respondent_id = r.id and rg.is_current = 1 limit 1
                  )
                  )
                )   
                limit 1
              ) as building_name,
            
              (select household.parent_id from geo household where household.id = (
                select rg.geo_id from respondent_geo rg where rg.respondent_id = r.id and rg.is_current = 1 limit 1
              )) as building_id,
            
              (select translated_text from translation_text where translation_id in 
                (select village.name_translation_id from geo village where village.id in 
                  (select building.parent_id from geo building where building.id in 
                    (select household.parent_id from geo household where household.id = (
                      select rg.geo_id from respondent_geo rg where rg.respondent_id = r.id and rg.is_current = 1 limit 1
                    ))
                  )
                ) 
                limit 1
              ) as village_name,
            
              (select building.parent_id from geo building where building.id in 
                (select household.parent_id from geo household where household.id = (
                  select rg.geo_id from respondent_geo rg where rg.respondent_id = r.id and rg.is_current = 1 limit 1
                ))
              ) as village_id
            from respondent r
            order by r.created_at asc
            limit $batchSize
            offset $offset;");
            $this->processBatch($respondents);
            $page++;
            $mightHaveMore = count($respondents) > 0;
        } while ($mightHaveMore);

        ReportService::saveFileStream($this->report, $fileName);
        // TODO: Save respondent photos as zip file

    }

    private function makeHeaders () {
        $this->defaultHeaders = [
            'id' => "respondent_id",
            'rname' => "respondent_name",
            'created_at' => "created_at",
            'updated_at' => "updated_at",
            "household_name" => "household_name",
            "household_id" => "household_id",
            "village_name" => "village_name",
            "village_id" => "village_id",
            "building_name" => "building_name",
            "building_id" => "building_id",
            "assigned_id" => "assigned_id"
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
        $this->headers = $this->defaultHeaders + $headers;
    }

    private function processBatch ($respondents) {
        $rows = [];

        $respondentIds = array_map(function ($r) {
            return $r->id;
        }, $respondents);

        $rcts = RespondentConditionTag::whereIn('respondent_id', $respondentIds)
            ->with('conditionTag')
            ->get();

        $rctMap = [];
        foreach($rcts as $rct){
            if(!isset($rctMap[$rct->respondent_id])){
                $rctMap[$rct->respondent_id] = [];
            }
            array_push($rctMap[$rct->respondent_id], $rct->conditionTag->name);
        }

        foreach ($respondents as $respondent) {
            $row = [];

            // Apply default headers
            foreach ($this->defaultHeaders as $key => $name) {
                $row[$key] = $respondent->$key;
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