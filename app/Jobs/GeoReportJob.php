<?php

namespace App\Jobs;

use App\Classes\CsvFileStream;
use App\Classes\Memoization;
use App\Models\Study;
use App\Services\ReportService;
use Log;
use App\Models\Report;
use App\Models\Geo;
use Ramsey\Uuid\Uuid;

class GeoReportJob extends Job
{

    protected $studyId;
    protected $report;
    protected $localeId;
    protected $maxDepth = 5;
    private $file = null;
    private $traverseGeoTree;
    protected $numParentsKey = 'numParents';

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
        $this->traverseGeoTree = Memoization::memoizeMax(function ($id, $maxDepth = 10, $depth = 0) {
            $tree = [];
            if ($depth > $maxDepth) return $tree;
            $geo = Geo::where('id', '=', $id)->with('nameTranslation')->first();
            if (isset($geo)) {
                array_push($tree, $geo);
                if (isset($geo->parent_id)) {
                    $tree = array_merge($tree, ($this->traverseGeoTree)($geo->parent_id, $maxDepth, $depth + 1));
                }
            }
            return $tree;
        }, 1000, function ($id) {
            return isset($id) ? $id : false;
        });
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        set_time_limit(170);
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
            $this->file->close();
            $this->report->save();
            $duration = microtime(true) - $startTime;
            Log::debug("GeoReportJob - finished: $this->studyId in $duration seconds");
        }
    }


    public function create(){

        $study = Study::with('locales', 'defaultLocale')->find($this->studyId);
        $this->localeId = $study->defaultLocale->id;
        $headers = [
            'id' => 'geo_id',
            'latitude' => 'latitude',
            'longitude' => 'longitude',
            'altitude' => 'altitude',
            $this->numParentsKey => $this->numParentsKey
        ];

        foreach ($study->locales as $locale) {
            $headers[$locale->language_name] = $locale->language_name;
        }

        for ($i = 0; $i < 5; $i++) {
            $key = 'parent' . $i;
            $headers[$key . "_id"] = $key . "_id";
            $headers[$key . "_name"] = $key . "_name";
        }

        $id = Uuid::uuid4();
        $filePath = storage_path("app/".$id . '.csv');
        $this->file = new CsvFileStream($filePath, $headers);
        $this->file->open();
        $this->file->writeHeader();

        // Run this until we've grabbed all of the existing geos
        $page = 0;
        $pageSize = 1000;
        do {
            $geos = Geo::whereNull('geo.deleted_at')
                ->limit($pageSize)
                ->offset($page * $pageSize)
                ->with('nameTranslation')
                ->get();
            $this->processBatch($geos);
            $page++;
            $mightHaveMore = $geos->count() > 0;
        } while ($mightHaveMore);

        // Sort by num parents from low to high
//        uasort($rows, function($a, $b){
//            return $a[$this->numParentsKey] - $b[$this->numParentsKey];
//        });

//        ReportService::saveDataFile($this->report, $headers, $rows);
        // TODO: create zip file with location images

    }

    public function processBatch (&$geos) {

        $rows = [];
        foreach ($geos as $geo) {
            $row = [];
            foreach (['latitude', 'id', 'longitude', 'altitude'] as $key) {
                $row[$key] = $geo->$key;
            }
            foreach ($geo->nameTranslation->translationText as $tt) {
                $row[$tt->locale->language_name] = $tt->translated_text;
            }
            $parents = ($this->traverseGeoTree)($geo->parent_id, 5);
            foreach ($parents as $index => $parent) {
                $key = "parent$index";
                $row[$key . "_id"] = $parent->id;
                foreach ($parent->nameTranslation->translationText as $tt) {
                    if ($tt->locale_id === $this->localeId) {
                        $row[$key . "_name"] = $tt->translated_text;
                        break;
                    }
                }
            }
            $row[$this->numParentsKey] = count($parents);
            $rows[$geo->id] = $row;
        }

        // Write to disk
        foreach ($rows as $row) {
            $this->file->writeRow($row);
        }

    }
}