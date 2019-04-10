<?php

namespace App\Jobs;

use App\Classes\Memoization;
use App\Services\ReportService;
use Log;
use App\Models\Report;
use App\Models\Geo;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class GeoReportJob extends Job
{
//    use InteractsWithQueue, SerializesModels;

    protected $studyId;
    protected $report;

    /**
     * Create a new job instance.
     *
     * @param  $studyId
     * @return void
     */
    public function __construct($studyId, $fileId)
    {
        Log::debug("GeoReportJob - constructing: $studyId");
        $this->studyId = $studyId;
        $this->report = new Report();
        $this->report->id = $fileId;
        $this->report->type = 'geo';
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
        Log::debug("GeoReportJob - handling: $this->studyId, $this->report->id");
        try{
            $this->create();
            $this->report->status = 'saved';
        } catch(Exception $e){
            $this->report->status = 'failed';
            $duration = microtime(true) - $startTime;
            Log::debug("GeoReportJob - failed: $this->studyId after $duration seconds");
        } finally{
            $this->report->save();
            $duration = microtime(true) - $startTime;
            Log::debug("GeoReportJob - finished: $this->studyId in $duration seconds");
        }
    }


    public function create(){

        $geosTranslations = Geo::whereNull('geo.deleted_at')
            ->join('translation_text', function($join){
                $join->on('translation_text.translation_id', '=', 'geo.name_translation_id');
                $join->whereNull('translation_text.deleted_at');
            })
            ->join('locale', 'locale.id', '=', 'translation_text.locale_id')
            ->select('geo.*', 'locale.language_name', 'translation_text.translated_text as name')
            ->get();

        $headers =[
            'id' => 'geo_id',
            'latitude' => 'latitude',
            'longitude' => 'longitude',
            'altitude' => 'altitude'
        ];

        $geoQuery = DB::table('geo')
            ->join('geo_type', 'geo_type.id', '=', 'geo.geo_type_id')
            ->join('translation_text', 'translation_text.translation_id', '=', 'geo.name_translation_id')
            ->select('geo.id', 'translation_text.translated_text as name', 'geo_type.name as type', 'geo.latitude', 'geo.longitude', 'geo.altitude', 'geo.parent_id');

        $geoHash = [];
        foreach($geoQuery->get() as $geo){
            $geoHash[$geo->id] = $geo;
        }

        $getGeoParent = Memoization::memoize(function($id){
            return DB::table('geo')
                ->join('geo_type', 'geo_type.id', '=', 'geo.geo_type_id')
                ->join('translation_text', 'translation_text.translation_id', '=', 'geo.name_translation_id')
                ->where('geo.id', '=', $id)
                ->select('geo.id', 'translation_text.translated_text as name', 'geo_type.name as type', 'geo.latitude', 'geo.longitude', 'geo.altitude', 'geo.parent_id')
                ->first();
        });

        $traverseGeoTree = Memoization::memoize(function ($startingId, $maxDepth=10) use ($geoHash){
            $tree = array();
            $id = $startingId;
            while(count($tree) < $maxDepth && $id !== null){
                $parent = $geoHash[$id];
                if($parent !== null) {
                    array_push($tree, $parent);
                    $id = $parent->parent_id;
                } else {
                    break;
                }
            }
            return $tree;
        });

        $geos = [];
        $numParentsKey = 'numParents';
        foreach($geosTranslations as $geo){
            $geo = $geo->toArray();
            if(!array_key_exists($geo['id'], $geos)){
                $geos[$geo['id']] = [];
            }
            foreach($geo as $key=>$val) {
                $geos[$geo['id']][$key] = $val;
            }
            $geos[$geo['id']][$geo['language_name']] = $geo['name'];
            $headers[$geo['language_name']] = $geo['language_name'];
            $parents = $traverseGeoTree($geo['parent_id']);
            foreach($parents as $index=>$parent){
                $key = "parent$index";
                $headers[$key."_id"] = $key."_id";
                $geos[$geo['id']][$key."_id"] = $parent->id;
                $headers[$key."_name"] = $key."_name";
                $geos[$geo['id']][$key."_name"] = $parent->name;
            }
            $headers[$numParentsKey] = $numParentsKey;
            $geos[$geo['id']][$numParentsKey] = count($parents);
        }

        $startTime = microtime(true);
        // Sort by num parents from low to high
        uasort($geos, function($a, $b) use($numParentsKey){
            return $a[$numParentsKey] - $b[$numParentsKey];
        });
        $duration = microtime(true) - $startTime;
        Log::debug("Sort took: $duration");

        ReportService::saveDataFile($this->report, $headers, $geos);
        // TODO: create zip file with location images

    }
}