<?php

namespace App\Reports;

use Composer\Autoload\ClassMapGenerator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
class ReportRunner {

  function __construct($report, string $studyId, array $config = []) {
    $this->report = $report;
    $this->config = $config;
    $this->config['studyId'] = $studyId;
    $this->validate();
  }

  private function validate () {
    $schema = self::mergeSchema($this->report);
    $validator = Validator::make($this->config, $schema);
    if ($validator->fails()) {
      throw $validator->errors();
    }
  }

  static function mergeSchema ($report) {
    return array_merge($report->defaultConfigSchema, $report->configSchema);
  }

  public function handle(bool $dryRun = false, bool $clean = true) {
    $this->report->config = $this->config;
    $this->report->handle($this->config);
    $this->report->close();
    if (!$dryRun) {
      $this->report->commit();
    }
    if ($clean) {
      $this->report->clean();
    }
  }

  static function allReports() {
    $reportMap = ReportRunner::reportMap();
    $reports = [];
    foreach ($reportMap as $class => $filename) {
      $inst = new $class;
      array_push($reports, [
        'name' => $inst->name,
        'configSchema' => self::mergeSchema($inst),
        'filename' => $filename
      ]);
    }
    Log::info(print_r($reports, true));
    return $reports;
  }

  static function reportMap() {
    $coreMap = ClassMapGenerator::createMap(__DIR__ . '/core', '/(?<!Report\.php)$/');
    $customMap = ClassMapGenerator::createMap(__DIR__ . '/custom', '/(?<!Report\.php)$/');
    return array_merge($coreMap, $customMap);
  }

  static function getReportInstance(string $name) {
    $reports = self::reportMap();
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
