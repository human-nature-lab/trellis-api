<?php

namespace App\Jobs;

use App\Services\ReportService;
use Log;
use Illuminate\Support\Facades\DB;
use App\Models\Report;

class RespondentReportJob extends Job
{

    protected $studyId;
    protected $report;
    protected $config;
    protected $maxGeoDepth=4;

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
            Log::debug("RespondentReportJob:  $this->studyId failed");
        } finally{
            $this->report->save();
            $duration = microtime(true) - $startTime;
            Log::debug("RespondentReportJob - finished: $this->studyId in $duration seconds");
        }
    }


    public function create(){

//        $respondents = DB::table('respondent')
//            ->join('study_respondent', 'study_respondent.respondent_id', '=', 'respondent.id')
//            ->where('study_respondent.study_id', '=', $this->studyId)
//            ->whereNull('respondent.deleted_at')
//            ->select('respondent.id',
//                'respondent.name as rname',
//                'respondent.created_at',
//                'respondent.updated_at')
//            ->get();

        $defaultHeaders = array(
            'id' => "respondent_id",
            'rname' => "respondent_name",
            'created_at' => "created_at",
            'updated_at' => "updated_at",
            "household_name" => "household_name",
            "household_id" => "household_id",
            "village_name" => "village_name",
            "village_id" => "village_id",
            "building_name" => "building_name",
            "building_id" => "building_id"
        );


        $respondents = DB::select("select r.id, r.name as rname, r.created_at, r.updated_at,   
              (select translated_text from translation_text where translation_id in 
                (select household.name_translation_id from geo household where household.id = r.geo_id) 
                limit 1
              ) as household_name,
            
              (select h1.id from geo h1 where h1.id = r.geo_id) as household_id,
            
              (select translated_text from translation_text where translation_id in 
                (select building.name_translation_id from geo building where building.id in 
                  (select household.parent_id from geo household where household.id = r.geo_id
                  )
                )   
                limit 1
              ) as building_name,
            
              (select household.parent_id from geo household where household.id = r.geo_id) as building_id,
            
              (select translated_text from translation_text where translation_id in 
                (select village.name_translation_id from geo village where village.id in 
                  (select building.parent_id from geo building where building.id in 
                    (select household.parent_id from geo household where household.id = r.geo_id)
                  )
                ) 
                limit 1
              ) as village_name,
            
              (select building.parent_id from geo building where building.id in 
                (select household.parent_id from geo household where household.id = r.geo_id)
              ) as village_id
            
            from respondent r;");


        $headers = array();
        $headers = array_replace($headers, $defaultHeaders);

        $conditions = DB::table("respondent_condition_tag")
            ->join('respondent', 'respondent.id', '=', 'respondent_condition_tag.respondent_id')
            ->join('condition_tag', 'condition_tag.id', '=', 'respondent_condition_tag.condition_tag_id')
            ->select("respondent.id", "condition_tag.name")
            ->get();

        $respondentConditions = [];
        foreach($conditions as $c){
            if(!isset($respondentConditions[$c->id])){
                $respondentConditions[$c->id] = [];
            }
            array_push($respondentConditions[$c->id], $c->name);
        }

        // map each respondent to a single row of the csv
        $rows = array_map(function ($respondent) use (&$defaultHeaders, &$headers, &$respondentConditions) {
            $newRow = array();
            foreach ($defaultHeaders as $key => $name){
                $newRow[$key] = $respondent->$key;
            }

            // Add conditions if there are any for this respondent
            if(isset($respondentConditions[$respondent->id])) {
                $rConditions = $respondentConditions[$respondent->id];
                foreach ($rConditions as $condition) {
                    $headers[$condition] = $condition;
                    $newRow[$condition] = true;
                }
            }

            return $newRow;
        }, $respondents);

        ReportService::saveDataFile($this->report, $headers, $rows);
        // TODO: Save respondent photos as zip file

    }

}