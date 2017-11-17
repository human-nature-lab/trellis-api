<?php

namespace App\Jobs;

use App\Services\ReportService;
use Log;
use App\Models\Report;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class EdgeReportJob extends Job
{
    use InteractsWithQueue, SerializesModels;

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
        Log::debug("EdgeReportJob - constructing: $studyId");
        $this->studyId = $studyId;
        $this->report = new Report();
        $this->report->id = $fileId;
        $this->report->type = 'edge';
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
        Log::debug("EdgeReportJob - handling: $this->studyId, $this->report->id");
        try{
//            ReportService::createEdgesReport($this->studyId, $this->export->id);
            $this->create();
            $this->report->status = 'saved';
        } catch(Exception $e){
            $this->report->status = 'failed';
            Log::debug("Edge report $this->studyId failed");
        } finally{
            $this->report->save();
            $duration = microtime(true) - $startTime;
            Log::debug("EdgeReportJob - finished: $this->studyId in $duration seconds");
        }
    }


    public function create(){

        $edges = DB::table('edge')
            ->join('respondent as sourceR', 'sourceR.id', '=', 'edge.source_respondent_id')
            ->join('respondent as targetR', 'targetR.id', '=', 'edge.target_respondent_id')
            ->join('edge_datum', 'edge_datum.edge_id', '=', 'edge.id')
            ->join('datum', 'datum.id', '=', 'edge_datum.datum_id')
            ->join('survey', 'survey.id', '=', 'datum.survey_id')
            ->join('geo as sGeo', 'sGeo.id', '=', 'sourceR.geo_id')
            ->join('geo as tGeo', 'tGeo.id', '=', 'targetR.geo_id')
            ->join('question', 'question.id', '=', 'datum.question_id')
            ->where('survey.study_id', '=', $this->studyId)
            ->select(
                'sourceR.id as sId',
                'targetR.id as rId',
                'question.var_name',
                'sGeo.latitude as sLat',
                'sGeo.longitude as sLong',
                'sGeo.altitude as sAlt',
                'tGeo.latitude as tLat',
                'tGeo.longitude as tLong',
                'tGeo.altitude as tAlt'
            )
            ->get();

        $headers = array(
            'sId' => 'Ego',
            'rId' => 'Alter',
            'var_name' => 'Question',
            'sLat' => 'Source Latitude',
            'sLong' => 'Source Longitude',
            'sAlt' => 'Source Altitude',
            'tLat' => 'Target Latitude',
            'tLong' => 'Target Longitude',
            'tAlt' => 'Target Altitude'
        );

        $rows = array_map(function ($r) use ($headers) {
            $newRow = array();
            foreach ($headers as $key => $name){
                $newRow[$key] = $r->$key;
            }
            return $newRow;
        }, $edges);


        ReportService::saveDataFile($this->report, $headers, $rows);
        // TODO: create zip file with location images

    }
}