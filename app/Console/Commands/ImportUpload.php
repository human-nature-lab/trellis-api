<?php

namespace App\Console\Commands;

use App\Library\FileHelper;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use SplFileObject;
use App\Models\UploadLog;
use App\Models\Upload;
use App\Library\FileMutex;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class ImportUpload extends BaseCommand {
  // USE: sudo -u www-data php artisan trellis:import:upload

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'trellis:import:upload {upload?*}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = "Check the uploads folder for unprocessed upload files and import any pending uploads.";

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle() {
    $this->mut = new FileMutex(storage_path('locks/uploads', 5 * 60 * 1000));

    if ($this->mut->isLocked()) {
      $this->error('uploads already running');
      return 2;
    }
    return $this->mut->do(function () {
      return $this->do();
    });
  }

  function do () {
    $uploadIds = $this->argument('upload');

    $n = count($uploadIds);
    $this->info("Starting import of $n uploads...");

    if ($n !== 0) {
      $uploads = Upload::whereIn('id', $uploadIds)->where('status', 'PENDING')->whereNull('deleted_at')->orderBy('created_at', 'asc')->get();
    } else {
      $uploads = Upload::where('status', 'PENDING')->whereNull('deleted_at')->orderBy('created_at', 'asc')->get();
    }

    app()->configure('upload');   // save overhead by only loading config when needed

    $config = [
      'uploadsPendingDir' => FileHelper::storagePath(config('upload.directory.pending')),
      'uploadsRunningDir' => FileHelper::storagePath(config('upload.directory.running')),
      'uploadsSuccessDir' => FileHelper::storagePath(config('upload.directory.success')),
      'uploadsFailedDir' => FileHelper::storagePath(config('upload.directory.failed'))
    ];

    foreach ($uploads as $upload) {
      $this->processUpload($upload, $config);
    }

    $n = count($uploads);
    $this->info("Imported $n uploads");

    return 0;
  }

  function getFirstPendingUpload() {
    $firstPendingUpload = Upload::where('deleted_at', null)
      ->where('status', 'PENDING')
      ->whereNull('deleted_at')
      ->orderBy('created_at', 'asc')
      ->first();

    return $firstPendingUpload;
  }

  function processUpload($upload, $config) {
    try {
      $upload->update(['status' => 'RUNNING']);

      $fileName = $upload->file_name;
      $this->info("Processing: $fileName");

      $fileName = $config['uploadsPendingDir'] . '/' . $fileName;
      if (!file_exists($fileName)) {
        throw new Exception("File, $fileName, not found!");
      }

      $this->info("extracting: $fileName");

      $zip = new ZipArchive;

      if (!$zip->open($fileName)) {
        throw new Exception("Unable to open zip file $fileName.");
      }

      if ($zip->numFiles > 1) {
        throw new Exception("More than one file contained in the archive $fileName, this is unexpected.");
      }

      $extractedFileName = $zip->getNameIndex(0);

      $zip->extractTo($config['uploadsRunningDir']);
      $zip->close();

      $file = new SplFileObject($config['uploadsRunningDir'] . '/' . $extractedFileName);

      try {
        DB::beginTransaction();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $assets = [];
        while (!$file->eof()) {
          $line = trim($file->fgets());
          if ($line != '') { // Skip empty lines
            $this->info($line);
            $row = json_decode($line, true);
            $tableName = $row['table_name'];
            $rowId = $row['id'];
            unset($row['table_name']);
            if ($tableName === 'asset') {
              $assets[] = $row;
            }
            try {
              DB::table($tableName)->insert($row);
              $this->info("New row, inserting.");
              $uploadLog = new UploadLog;
              $uploadLog->upload_id = $upload->id;
              $uploadLog->table_Name = $tableName;
              $uploadLog->operation = 'INSERT';
              $uploadLog->row_id = $rowId;
              $uploadLog->save();
            } catch (QueryException $e) {
              $errorCode = $e->errorInfo[1];
              $this->info("errorCode: $errorCode");
              if ($errorCode == 1062) {
                $this->info("Duplicate entry on insert, need to update.");
                $previousRow = json_encode(DB::table($tableName)->find($rowId));
                $uploadLog = new UploadLog;
                $uploadLog->upload_id = $upload->id;
                $uploadLog->table_Name = $tableName;
                $uploadLog->operation = 'UPDATE';
                $uploadLog->row_id = $rowId;
                $uploadLog->previous_row = $previousRow;
                $uploadLog->updated_row = json_encode($row);
                $uploadLog->save();
                $rowId = $row['id'];
                unset($row['id']);
                DB::table($tableName)->where('id', $rowId)->update($row);
              }
            }
          }
        }

        // Check md5 hash of assets
        foreach($assets as $asset) {
          $assetId = $asset['id'];
          $expectedMd5 = $asset['md5_hash'];
          $filePath = storage_path('assets/' . $assetId);
          $actualMd5 = md5_file($filePath);
          if ($expectedMd5 !== $actualMd5) {
            throw new Exception("MD5 hash mismatch for asset: $assetId. Expected: $expectedMd5, Actual: $actualMd5");
          }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Check foreign keys
        ob_start();

        $res = Artisan::call('trellis:check:mysql:foreignkeys');
        Log::info("Foreign key check result: $res");

        $inconsistencies = ob_get_clean();

        if (count(json_decode($inconsistencies, true))) {
          echo $inconsistencies;
          throw new Exception("Foreign key checks failed:" . PHP_EOL . $inconsistencies);
        }

        DB::commit();
      } catch (Exception $e) {
        DB::rollBack();
        throw $e;
      }

      // Close file
      $file = null;

      // Mark upload as successful in database
      $upload->update(['status' => 'SUCCESS']);

      // Move processed file to the upload_success directory
      $successPath = $config['uploadsSuccessDir'] . '/' . $upload->file_name;
      rename($fileName, $successPath);

      // Clean up working directory
      unlink($config['uploadsRunningDir'] . '/' . $extractedFileName);
    } catch (Exception $exception) {
      $this->error($exception->getMessage());
      $upload->update([
        'status' => 'FAILED',
        'error_message' => $exception->getMessage(),
        'error_code' => $exception->getCode(),
        'error_trace' => $exception->getTraceAsString(),
        'error_line' => $exception->getLine()
      ]);

      $from = $config['uploadsPendingDir'] . '/' . $upload->file_name;
      $to = $config['uploadsFailedDir'] . '/' . $upload->file_name;

      if (file_exists($from)) {
        // Move file to the uploads_failed directory
        rename($from, $to);
      }
    }
  }
}
