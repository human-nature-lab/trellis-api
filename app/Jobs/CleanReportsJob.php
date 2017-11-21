<?php

namespace App\Jobs;

use App\Models\Report;
use App\Models\ReportFile;
use Log;

class CleanReportsJob extends Job
{

    protected $oldestDate;

    /**
     * Creates the Job for cleaning reports before the $oldestDate supplied. Defaults to cleaning reports that are older
     * than one week.
     * @param $oldestDate
     */
    public function __construct($oldestDate)
    {
        if($oldestDate) {
            $this->oldestDate = $oldestDate;
        } else {
            // Default to removing reports that are one week old
            $this->oldestDate = new DateTime;
            $oneWeek = new DateInterval('P1W');
            $this->oldestDate = $this->oldestDate->sub($oneWeek);
        }
    }

    public function handle(){
        $startTime = microtime(true);
        Log::debug("CleanReportsJob - handling");
        $this->deleteOlderThan($this->oldestDate);
        $duration = microtime(true) - $startTime;
        Log::debug("CleanReportsJob - finished in $duration seconds");
    }


    public function deleteOlderThan($date){
        $reportsToRemove = Report::where('report.updated_at', '<=', $date)->whereNull('deleted_at')->get();
        foreach($reportsToRemove as $report){
            $files = ReportFile::where('report_id', '=', $report->id)->whereNull('deleted_at')->get();
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