<?php

namespace App\Jobs;


use App\Library\CsvFileWriter;
use App\Services\ReportService;
use App\Models\Report;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\DB;


class AssetReportJob extends Job {

  public $report;
  protected $studyId;
  private $file;

  public function __construct($studyId, $config) {
    Log::debug("AssetReportJob - constructing: $studyId");
    $this->studyId = $studyId;
    $this->report = new Report();
    $this->report->id = Uuid::uuid4();
    $this->report->study_id = $studyId;
    $this->report->type = 'asset';
    $this->report->status = 'queued';
    $this->report->save();
  }

  public function handle () {
    set_time_limit(0);
    $startTime = microtime(true);
    Log::debug("AssetReportJob - handling: $this->studyId, $this->report->id");
    try{
        $this->create();
        $this->report->status = 'saved';
    } catch(\Exception $e){
        $this->report->status = 'failed';
        $duration = microtime(true) - $startTime;
        Log::debug("AssetReportJob - failed: $this->studyId after $duration seconds");
    } finally{
        $this->report->save();
        if (isset($this->file)) {
            $this->file->close();
        }
        $duration = microtime(true) - $startTime;
        Log::debug("AssetReportJob - finished: $this->studyId in $duration seconds");
    }
  }

  public function create () {
    $id = Uuid::uuid4();
    $fileName = $id . '.csv';
    $filePath = storage_path('app/' . $fileName);

    $this->file = new CsvFileWriter($filePath, ['id', 'file_name', 'type', 'mime_type', 'size', 'md5_hash', 'created_at', 'updated_at', 'deleted_at']);
    $q = DB::table('asset');
    $this->file->open();
    $this->file->writeHeader();
    foreach ($q->cursor() as $asset) {
      Log::info("writing asset: $asset->id");
      $this->file->writeRow([
        $asset->id,
        $asset->file_name,
        $asset->type,
        $asset->mime_type,
        $asset->size,
        $asset->md5_hash,
        $asset->created_at,
        $asset->updated_at,
        $asset->deleted_at
      ]);
    }
    ReportService::saveFileStream($this->report, $fileName);
  }
}