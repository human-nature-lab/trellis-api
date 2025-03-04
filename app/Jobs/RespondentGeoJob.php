<?php

namespace App\Jobs;

use App\Library\CsvFileWriter;
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
    public $report;
    private $headers;
    private $file;

    /**
     * Create a new job instance.
     *
     * @param  $studyId
     * @return void
     */
    public function __construct ($studyId, $config) {
        Log::debug("RespondentGeoReport - constructing: $studyId");
        $this->studyId = $studyId;
        $this->report = new Report();
        $this->report->id = Uuid::uuid4();
        $this->report->type = 'respondent_geo';
        $this->report->status = 'queued';
        $this->report->study_id = $this->studyId;
        $this->report->save();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle () {
        set_time_limit(0);
        $startTime = microtime(true);
        Log::debug("RespondentGeoReport - handling: $this->studyId, $this->report->id");
        try{
            $this->create();
            $this->report->status = 'saved';
        } catch(Exception $e){
            $this->report->status = 'failed';
            Log::debug("RespondentGeoReport $this->studyId failed");
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
        $this->file = new CsvFileWriter($filePath, $this->headers);
        $this->file->open();
        $this->file->writeHeader();


        $tmpTableName = 'respondent_geo_job_qd_rg';
        DB::statement('drop table if exists '.$tmpTableName);
        $q = 'create table '.$tmpTableName.' (survey_id int, question_id int, val text, respondent_geo_id int) as 
        select qd.survey_id, qd.question_id, d.val, d.respondent_geo_id from question_datum qd
        inner join datum d on d.question_datum_id = qd.id
        where d.respondent_geo_id is not null and d.deleted_at is null and qd.deleted_at is null';
        DB::statement($q);

        $q = "create index idx_qd_rg_rgeo_id_val on $tmpTableName (respondent_geo_id, val(100))";
        DB::statement($q);

        $q = RespondentGeo::withTrashed()
            ->addSelect(DB::raw('*'))
            ->addSelect(DB::raw("(select qd_rg.question_id from $tmpTableName qd_rg 
            where qd_rg.respondent_geo_id = respondent_geo.id and (qd_rg.val = 'Add respondent geo' or qd_rg.val = 'Move respondent') limit 1
            ) as added_question_id"))
            ->addSelect(DB::raw("(select qd_rg.survey_id from $tmpTableName qd_rg 
                where qd_rg.respondent_geo_id = respondent_geo.id and (qd_rg.val = 'Add respondent geo' or qd_rg.val = 'Move respondent') limit 1
                ) as added_survey_id"))
            ->addSelect(DB::raw("(select qd_rg.question_id from $tmpTableName qd_rg 
                where qd_rg.respondent_geo_id = respondent_geo.id and qd_rg.val = 'Remove respondent geo' limit 1
                ) as removed_question_id"))
            ->addSelect(DB::raw("(select qd_rg.survey_id from $tmpTableName qd_rg 
                where qd_rg.respondent_geo_id = respondent_geo.id and qd_rg.val = 'Remove respondent geo' limit 1
                ) as removed_survey_id"));

        $batchSize = (int)env('RESPONDENT_GEO_REPORT_BATCH_SIZE', 2000);
        $q->chunk($batchSize, function ($rGeos) {
            $rGeos = $rGeos->map(function ($rGeo) {
                $rGeo->is_current = $rGeo->is_current === 1;
                return $rGeo;
            })->toArray();
            $this->file->writeRows($rGeos);
        });
        DB::statement('drop table if exists '.$tmpTableName);
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
            'created_at' => 'created_at',
            'deleted_at' => 'deleted_at',
            'added_question_id' => 'added_question_id',
            'added_survey_id' => 'added_survey_id',
            'removed_question_id' => 'removed_question_id',
            'removed_survey_id' => 'removed_survey_id'
        ];
    }
}