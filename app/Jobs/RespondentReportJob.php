<?php

namespace App\Jobs;

use App\Services\ReportService;
use Log;
use Illuminate\Support\Facades\DB;
use App\Models\Report;
use App\Models\ReportFile;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\FileService;
use App\Classes\Memoization;
use Ramsey\Uuid\Uuid;

class RespondentReportJob extends Job
{
    use InteractsWithQueue, SerializesModels;

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

        $respondents = DB::table('respondent')
            ->join('study_respondent', 'study_respondent.respondent_id', '=', 'respondent.id')
            ->where('study_respondent.study_id', '=', $this->studyId)
            ->whereNull('respondent.deleted_at')
            ->select('respondent.id',
                'respondent.name as rname',
                'respondent.created_at',
                'respondent.updated_at',
                'respondent.geo_id')
            ->get();

        $defaultHeaders = array(
            'id' => "Respondent id",
            'rname' => "Respondent name",
            'created_at' => "Created at",
            'updated_at' => "Updated at",
        );

        $getGeoParent = Memoization::memoize(function($id){
            return DB::table('geo')
                ->join('geo_type', 'geo_type.id', '=', 'geo.geo_type_id')
                ->join('translation_text', 'translation_text.translation_id', '=', 'geo.name_translation_id')
                ->where('geo.id', '=', $id)
                ->select('geo.id', 'translation_text.translated_text as name', 'geo_type.name as type', 'geo.latitude', 'geo.longitude', 'geo.altitude', 'geo.parent_id')
                ->first();
        });

        $traverseGeoTree = function ($startingId, $maxDepth) use ($getGeoParent){
            $tree = array();
            $id = $startingId;
            while(count($tree) < $maxDepth && $id !== null){
                $parent = $getGeoParent($id);
                if($parent !== null) {
                    array_push($tree, $parent);
                    $id = $parent->parent_id;
                } else {
                    break;
                }
            }
            return $tree;
        };

        $headers = array();
        $headers = array_replace($headers, $defaultHeaders);

        $conditionsGroupedByRespondentId = DB::table('respondent_condition_tag')
            ->join('respondent', 'respondent.id', '=', 'respondent_condition_tag.respondent_id')
            ->join('condition_tag', 'condition_tag.id', '=', 'respondent_condition_tag.condition_tag_id')
            ->select('respondent.id', DB::raw("group_concat(condition_tag.name SEPARATOR ';') as conditions"))
            ->groupBy('respondent.id');

        $respondent_conditions = array_reduce($conditionsGroupedByRespondentId->get(), function($agg, $r){
            $agg[$r->id] = explode(';', $r->conditions);
            return $agg;
        }, array());

        // map each respondent to a single row of the csv
        $maxDepth = $this->maxGeoDepth;
        $rows = array_map(function ($respondent) use ($defaultHeaders, &$headers, &$respondent_conditions, &$maxDepth, $traverseGeoTree) {
            $newRow = array();
            foreach ($defaultHeaders as $key => $name){
                $newRow[$key] = $respondent->$key;
            }

            $geoTree = $traverseGeoTree($respondent->geo_id, $maxDepth);
            foreach($geoTree as $level => $geo){
                $key = "geo_level_" . $level;
                $headers[$key] = $key;
                $newRow[$key] = $geo->name;
            }

            // Add conditions if there are any for this respondent
            if(array_key_exists($respondent->id, $respondent_conditions)) {
                $conditions = $respondent_conditions[$respondent->id];
                foreach ($conditions as $condition) {
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