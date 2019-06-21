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
    private $headers;
    private $config;
    protected $numParentsKey = 'numParents';

    /**
     * Create a new job instance.
     *
     * @param  $studyId
     * @return void
     */
    public function __construct($studyId, $fileId, $config)
    {
        Log::debug("GeoReportJob - constructing: $studyId");
        $this->config = $config;
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
    public function handle () {
        set_time_limit(0);
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
            if (isset($this->file)) {
                $this->file->close();
            }
            $this->report->save();
            $duration = microtime(true) - $startTime;
            Log::debug("GeoReportJob - finished: $this->studyId in $duration seconds");
        }
    }


    public function create(){

        $this->localeId = ReportService::extractLocaleId($this->config, $this->studyId);

        $this->makeHeaders();

        $id = Uuid::uuid4();
        $fileName = $id . '.csv';
        $filePath = storage_path('app/' . $fileName);
        $this->file = new CsvFileStream($filePath, $this->headers);
        $this->file->open();
        $this->file->writeHeader();

        // Run this until we've grabbed all of the existing geos
        $q = Geo::whereNull('geo.deleted_at')->with('nameTranslation', 'geoType');

        foreach ($q->cursor() as $geo) {
            $geos = [$geo];
            $this->processBatch($geos);
        }

        ReportService::saveFileStream($this->report, $fileName);
        // TODO: create zip file with location images

    }

    private function makeHeaders () {
        $study = Study::with('locales', 'geoTypes')->find($this->studyId);
        $headers = [
            'id' => 'geo_id',
            'latitude' => 'latitude',
            'longitude' => 'longitude',
            'altitude' => 'altitude',
            'type' => 'type',
            $this->numParentsKey => $this->numParentsKey
        ];

        foreach ($study->locales as $locale) {
            $headers[$locale->language_name] = $locale->language_name;
        }

        // TODO: this could be a separate query to only get used geo types so we don't have empty columns
        foreach ($study->geoTypes as $geoType) {
            $headers[$geoType->id . '_id'] = ReportService::makeTextSafe($geoType->name) . '_id';
            $headers[$geoType->id . '_name'] = ReportService::makeTextSafe($geoType->name) . '_name';
        }
        $this->headers = $headers;
    }

    public function processBatch (&$geos) {

        $rows = [];
        foreach ($geos as $geo) {
            $row = [];
            foreach (['latitude', 'id', 'longitude', 'altitude'] as $key) {
                $row[$key] = $geo->$key;
            }
            $row['type'] = $geo->geoType->name;
            foreach ($geo->nameTranslation->translationText as $tt) {
                $row[$tt->locale->language_name] = $tt->translated_text;
            }
            $parents = ($this->traverseGeoTree)($geo->parent_id, 5);
            foreach ($parents as $index => $parent) {
                $key = $parent->geo_type_id;
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
        $this->file->writeRows($rows);

    }
}