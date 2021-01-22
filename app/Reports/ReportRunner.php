<?php

namespace App\Reports;

use Composer\Autoload\ClassMapGenerator;
use Log;

class ReportRunner {

  function __construct($report, string $studyId) {
    $this->report = $report;
    $report->studyId = $studyId;
  }

  public function handle(array $config = [], bool $dryRun = false, bool $clean = true) {
    $this->report->handle($config);
    $this->report->close();
    if (!$dryRun) {
      $this->report->commit();
    }
    if ($clean) {
      $this->report->clean();
    }
  }

  static function allReportNames() {
    $reports = ReportRunner::allReports();
    $names = [];
    foreach ($reports as $class => $filename) {
      array_push($names, $filename);
    }
    Log::info(print_r($names, true));
    return $names;
  }

  static function allReports() {
    return ClassMapGenerator::createMap(__DIR__, '/(?<!Report\.php)$/');
  }

  static function getReportInstance(string $name) {
    $reports = self::allReports();
    foreach ($reports as $class => $filename) {
      $className = $class;
      require_once $filename;
      if (class_exists($className)) {
        $inst = new $className;
        if ($inst->name === $name) {
          return $inst;
        }
      }
    }
    return null;
  }
}
