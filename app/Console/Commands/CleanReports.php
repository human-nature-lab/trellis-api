<?php

namespace App\Console\Commands;

use App\Models\Report;
use App\Models\ReportFile;
use DateInterval;
use DateTime;
use Illuminate\Console\Command;

class CleanReports extends Command {

  protected $signature = 'trellis:clean:reports {--keep-last-days=14 : The number of days of reports to keep}';

  protected $description = 'Remove reports that were created earlier than the oldest date';

  /**
   * @throws \Exception
   */
  public function handle () {

    $daysToSubtract = $this->option('keep-last-days');

    $oldestDate = new DateTime;
    $interval = new DateInterval('P' . $daysToSubtract . 'D');
    $oldestDate = $oldestDate->sub($interval);

    $startTime = microtime(true);
    $this->deleteOlderThan($oldestDate);
    $duration = microtime(true) - $startTime;
    $this->info("Finished cleaning reports in $duration seconds");
  }


  public function deleteOlderThan ($date) {
    $reportsToRemove = Report::withTrashed()->whereDate('updated_at', '<=', $date)->get();
    $this->info($reportsToRemove);
    foreach($reportsToRemove as $report){
      $files = ReportFile::where('report_id', '=', $report->id)->get();
      foreach($files as $file){
        $filePath = storage_path("app/$file->file_name");
        if(file_exists($filePath)){
          unlink($filePath);
        }
        $file->delete();
      }
      $report->delete();
    }
  }

}