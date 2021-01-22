<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Reports\ReportRunner;
use DB;

class RunReport extends Command {

    protected $signature = 'trellis:run:report 
                              { --name=* : The name of the report to run }
                              { --config=* : JSON config to pass to this report }
                              { --study=* : A study id to run these reports on }
                              { --dry : If present no data will be kept permanently. Will still write results to temp files and delete them. }';

    protected $description = 'Run any report defined in Trellis';

    public function handle () {
      $studies = $this->option('study');
      if (count($studies) === 0) {
        $studies = $this->getAllStudyIds();
      }
      $names = $this->option('name');
      $configs = $this->option('config');
      if (isset($names)) {
        foreach ($studies as $studyId) {
          foreach ($names as $i => $name) {
            $this->info("Running $name report for study $studyId.");
            $config = $configs[$i];
            if (isset($config) && $config !== '') {    
              $config = json_decode($config);
              $this->info(print_r($config, true));
            } else {
              $config = [];
            }
            $isDry = $this->option('dry');
            $report = ReportRunner::getReportInstance($name);
            if (isset($report)) {
              $runner = new ReportRunner($report, $studyId);
              $runner->handle((array)$config, $isDry);
            }
          }
        }
      } else {
        $this->error('Must supply names of reports to run');
      }
    }
    
    public function getAllStudyIds () {
      return DB::table('study')->select('id')->get()->map(function ($s) { return $s->id; }); 
    }

}
