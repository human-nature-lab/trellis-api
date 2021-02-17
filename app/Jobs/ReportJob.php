<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use App\Reports\ReportRunner;

class ReportJob extends Job {

    public $studyId;
    public $reportName;
    public $config;

    public function __construct(string $reportName, string $studyId, array $config) {
      $this->reportName = $reportName;
      $this->studyId = $studyId;
      $this->config = $config;
    }

    public function handle () {
      Log::debug("ReportJob - handling: $this->studyId, $this->reportName");
      set_time_limit(0);
      $startTime = microtime(true);
      $report = ReportRunner::getReportInstance($this->reportName);
      $config = array_merge([ 'studyId' => $this->studyId ], $this->config);
      if (isset($report)) {
        $runner = new ReportRunner($report, $this->studyId, $config);
        $runner->handle();
        $duration = date("H:i:s", microtime(true) - $startTime);
        Log::debug("ReportJob - done: $this->studyId, $this->reportName in $duration");
      } else {
        Log::debug("No report found for $this->reportName");
      }
    }
}