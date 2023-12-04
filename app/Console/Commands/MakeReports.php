<?php

namespace App\Console\Commands;

use App\Jobs\ActionReportJob;
use App\Jobs\EdgeReportJob;
use App\Jobs\FormReportJob;
use App\Jobs\GeoReportJob;
use App\Jobs\InterviewReportJob;
use App\Jobs\RespondentGeoJob;
use App\Jobs\RespondentReportJob;
use App\Jobs\TimingReportJob;
use App\Models\Form;
use App\Models\Report;
use App\Models\Study;
use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\Job;
// use Laravel\Lumen\Routing\DispatchesJobs;
use Illuminate\Support\Facades\Log;
use Queue;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class MakeReports extends Command {
    protected $signature = 'trellis:make:reports 
      {--skip-main} 
      {--skip-forms}
      {--unpublished : Include unpublished forms}
      {--locale=}
      {--form=}
      {--study=*}';
    protected $description = 'Run each type of report once to get the latest data';

    public function handle () {
        ini_set('memory_limit', '-1');
        Queue::after(function ($connection, $job, $data) {
            Log::debug("Finished job: ", $job->id, $data);
        });

        $studyIds = $this->option('study');

        if (count($studyIds) === 0) {
          $studyIds = DB::table('study')->select('id')->whereNull('deleted_at')->get()->map(function ($s) { return $s->id; });
        }

        $studyCount = count($studyIds);
        $this->info("Generating reports for $studyCount studies");

        foreach ($studyIds as $studyId) {
          $this->info("Generating reports for study $studyId");
          $this->makeStudyReports($studyId);
        }

    }

    private function makeStudyReports ($studyId) {
      $remainingJobIds = [];
      $study = Study::where("id", "=", $studyId)->with("defaultLocale")->first();
      $localeId = $this->option('locale') ?: '48984fbe-84d4-11e5-ba05-0800279114ca';
      if (!isset($study)) {
        throw new \Exception('Study id must be valid');
      }
      $mainJobConstructors = [TimingReportJob::class, RespondentGeoJob::class, InterviewReportJob::class, EdgeReportJob::class, GeoReportJob::class, ActionReportJob::class, RespondentReportJob::class];

      if (!$this->option('skip-main')) {
        foreach ($mainJobConstructors as $constructor){
          $reportId = Uuid::uuid4();
          array_push($remainingJobIds, $reportId);
          $config = new \stdClass();
          $config->localeId = $localeId;
          $reportJob = new $constructor($studyId, $reportId, $config);
          $this->info("Queued $constructor");
          $reportJob->handle();
          $this->info("Finished $constructor");
        }
      }

      if (!$this->option('skip-forms')) {
        if ($this->option('form')) {
          $formIds = [$this->option('form')];
        } else {
          $formQuery = Form::select('id')->
            whereIn('form_master_id', function ($q) use ($studyId) {
              $q->
                select('form_master_id')->
                from('study_form')->
                where('study_id', $studyId);
            })->
            whereNull('deleted_at');
          if (!$this->option('unpublished')) {
            $formQuery = $formQuery->where('is_published', true);
          }
          $formIds = $formQuery->
            get()->
            map(function ($item) {
              return $item->id;
            });
        }

        $config = new \stdClass();
        $config->studyId = $studyId;
        $config->useChoiceNames = true;
        $config->locale = $study->defaultLocale->id;
        $config->locale = "48984fbe-84d4-11e5-ba05-0800279114ca";

        foreach ($formIds as $formId){
          $reportJob = new FormReportJob($studyId, $formId, $config);
          array_push($remainingJobIds, $reportJob->report->id);
          $this->info("Queued FormReportJob for form, $formId");
          $reportJob->handle();
          $this->info("Finished FormReportJob for form, $formId");
        }
      }

      $this->info("Completed reports for study $studyId");
    }

}
