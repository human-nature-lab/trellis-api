<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Reports\ReportRunner;
use DB;

class RunReport extends Command {

    protected $signature = 'trellis:report:run
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
      $runners = [];
      if (isset($names)) {
        foreach ($studies as $studyId) {
          foreach ($names as $i => $name) {
            $this->info("Running $name report for study $studyId.");
            if (isset($configs[$i]) && $configs[$i] !== '') {    
              $config = json_decode($configs[$i]);
              $this->info(print_r($config, true));
            } else {
              $config = [];
            }
            $report = ReportRunner::getReportInstance($name);
            $config = array_merge([ 'studyId' => $studyId ], (array)$config);
            if (isset($report)) {
              $runner = new ReportRunner($report, $studyId, $config);
              array_push($runners, $runner);
            }
          }
        }
      } else {
        $this->error('Must supply names of reports to run');
      }
      foreach ($runners as $runner) {
        $isDry = $this->option('dry');
        $runner->handle($isDry);
      }
    }
    
    public function getAllStudyIds () {
      return DB::table('study')->select('id')->get()->map(function ($s) { return $s->id; }); 
    }

}
