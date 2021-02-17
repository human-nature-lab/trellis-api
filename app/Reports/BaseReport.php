<?php

namespace App\Reports;

use App\Library\CsvFileWriter;
use App\Models\Report;
use App\Models\ReportFile;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\DB;

class BaseReport {
  
  public $files = [];
  public $defaultConfigSchema = [
    'studyId' => 'string|exists:study,id'
  ];
  public $configSchema = [];
  public $config = [];
  
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

  public function streamTable (string $table, string $name = '', string $type = 'data', int $chunkSize = 400) {
    if ($name === '') {
      $name = $table;
    }
    $columns = $this->tableColumns($table);
    $this->streamQuery($this->DB()->table($table)->orderBy('id'), $columns, $name, $type, $chunkSize);
  }
  
  public function mapQuery ($cb, $query, array $headers, string $name = 'name', string $type = 'data', int $chunkSize = 400) {
    $file = $this->createCsvFile($name, $type);
    $file->setHeaders($headers);
    $file->writeHeader();
    $query->chunk($chunkSize, function ($records) use ($file, $cb) {
      foreach ($records as $record) {
        $file->writeRow($cb($record));
      }
    });
  }

  public function streamQuery ($query, array $headers, string $name = '', string $type = 'data', int $chunkSize = 400) {
    $this->mapQuery(function ($record) {
      return $record;
    }, $query, $headers, $name, $type, $chunkSize);
  }

  public function createCsvFile (string $name = '', string $type = 'data'): CsvFileWriter {
    $fileName = Uuid::uuid4() . '.csv';
    if ($name !== '') {
      $fileName = $name . '_' . $fileName;
    }
    $descriptor = $this->createFileDescriptor($fileName, $type, 'csv');
    $descriptor->file = new CsvFileWriter($descriptor->temp);
    $descriptor->file->open();
    array_push($this->files, $descriptor);
    return $descriptor->file;
  }

  public function createFileDescriptor (string $fileName, string $dataType, string $fileType) {
    $tempPath = storage_path('temp/' . $fileName);
    $finalPath = storage_path('app/' . $fileName);
    return (object)[
      'name' => $fileName,
      'temp' => $tempPath,
      'path' => $finalPath,
      'data_type' => $dataType,
      'file_type' => $fileType,
    ];
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
      $report->name = $this->name;
      $report->status = 'complete';
      $report->study_id = $this->config['studyId'];
      $report->config = json_encode($this->config);
      $reportFiles = [];
      foreach ($this->files as $file) {
        $size = filesize($file->temp);
        $reportFile = new ReportFile();
        $reportFile->fill([
          'id' => Uuid::uuid4(),
          'report_id' => $report->id,
          'file_name' => $file->name,
          'data_type' => $file->data_type,
          'file_type' => $file->file_type,
          'size' => $size,
        ]);
        $reportFiles[] = $reportFile;
        if (!rename($file->temp, $file->path)){
          throw new Exception("Unable to rename file from $file->temp to $file->path");
        }
      }
      $report->save();
      $report->files()->saveMany($reportFiles);
    });
    // TODO: Should we do some cleanup on failure?
  }

}