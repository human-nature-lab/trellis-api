<?php

namespace App\Reports;

use App\Library\CsvFileWriter;
use App\Models\Report;
use App\Models\ReportFile;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Base {
  
  public $files = [];
  public $defaultConfigSchema = [
    'studyId' => 'string'
  ];
  public $configSchema = [];
  
  public function DB () {
    return DB::connection('reports');
  }
  
  public function Model (string $modelName) {
    $namespacedModel = "\\App\\Models\\$modelName";
    return $namespacedModel::on('reports');
  }

  public function tableColumns (string $table): array {
    return $this->DB()->getSchemaBuilder()->getColumnListing($table);
  }

  public function streamTable (string $table, string $type = 'data', int $chunkSize = 400) {
    $columns = $this->tableColumns($table);
    $this->streamQuery($this->DB()->table($table)->orderBy('id'), $columns, $type, $chunkSize);
  }
  
  public function mapQuery ($cb, $query, array $headers, string $type = 'data', int $chunkSize = 400) {
    $file = $this->createFile($type);
    $file->setHeaders($headers);
    $file->writeHeader();
    $query->chunk($chunkSize, function ($records) use ($file, $cb) {
      foreach ($records as $record) {
        $file->writeRow($cb($record));
      }
    });
  }

  public function streamQuery ($query, array $headers, string $type = 'data', int $chunkSize = 400) {
    $this->mapQuery(function ($record) {
      return $record;
    }, $query, $headers, $type, $chunkSize);
  }

  public function createFile (string $type = 'data'): CsvFileWriter {
    $name = Uuid::uuid4() . '.csv';
    $tempPath = storage_path('temp/' . $name);
    $finalPath = storage_path('app/' . $name);
    $file = new CsvFileWriter($tempPath);
    $file->open();
    array_push($this->files, (object)[
      'name' => $name,
      'temp' => $tempPath,
      'path' => $finalPath,
      'type' => $type,
      'file' => $file
    ]);
    return $file;
  }

  public function close () {
    foreach ($this->files as $file) {
      $file->file->close();
    }
  }

  public function clean () {
    // Remove any leftover temp files that were created while processing
    foreach ($this->files as $file) {
      if (file_exists($file->temp)) {
        unlink($file->temp);
      }
    }
  }

  public function commit () {
    DB::transaction(function () {
      $report = new Report();
      $report->id = Uuid::uuid4();
      $report->type = $this->name;
      $report->status = 'complete';
      $report->study_id = $this->studyId;
      $reportFiles = [];
      foreach ($this->files as $file) {
        $reportFile = new ReportFile();
        $reportFile->id = Uuid::uuid4();
        $reportFile->report_id = $report->id;
        $reportFile->file_name = $file->name;
        $reportFiles[] = $reportFile;
        if (!rename($file->temp, $file->path)){
          throw new Error("Unable to rename file from $file->temp to $file->path");
        }
      }
      $report->save();
      $report->files()->saveMany($reportFiles);
    });
    // TODO: Should I do some cleanup on failure?
  }

}