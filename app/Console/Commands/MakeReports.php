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
use Log;
use DB;
use Queue;
use Ramsey\Uuid\Uuid;

class MakeReports extends Command {
    protected $signature = 'trellis:make:reports {--study=*} {--skip-main} {--skip-forms} {--locale=} {--form=*}';
    protected $description = 'Run each type of report once to get the latest data';

  public function handle () {
    ini_set('memory_limit', '-1');
    Queue::after(function ($connection, $job, $data) {
      Log::debug("Finished job: ", $job->id, $data);
    });
    $studyIds = $this->option('study');
    if (count($studyIds) === 0) {
      $studyIds = DB::table('study')->select('id')->get()->map(function ($s) { return $s->id; });
    }
    $studyCount = count($studyIds);
    $this->info("Generating reports for $studyCount studies");
    foreach ($studyIds as $studyId) {
      $this->info("Generating reports for study $studyId");
      $this->makeStudyReports($studyId);
    }
  }

    public function makeStudyReports ($studyId) {

        $remainingJobIds = [];
        $study = Study::where("id", "=", $studyId)->with("defaultLocale")->first();
        $localeId = $this->option('locale') ?: '48984fbe-84d4-11e5-ba05-0800279114ca';
        if (!isset($study)) {
            throw Exception('Study id must be valid');
        }
        $mainJobConstructors = [TimingReportJob::class, RespondentGeoJob::class, InterviewReportJob::class, EdgeReportJob::class, GeoReportJob::class, ActionReportJob::class, RespondentReportJob::class];

        if (!$this->option('skip-main')) {
            foreach ($mainJobConstructors as $constructor){
                $reportId = Uuid::uuid4();
                array_push($remainingJobIds, $reportId);
                $config = new \stdClass();
                $config->localeId = $localeId;
                $reportJob = new $constructor($studyId, $reportId, $config);
                $reportJob->handle();
                $this->info("Queued $constructor");
            }
        }

        if (!$this->option('skip-forms')) {
            if ($this->option('form')) {
                $formIds = $this->option('form');
            } else {
                $formIds = Form::select('id')->whereIn('id', function ($q) use ($studyId) {
                    $q->select('form_master_id')->from('study_form')->where('study_id', $studyId);
                })->whereNull('deleted_at')->where('is_published', true)->get()->map(function ($item) {
                    return $item->id;
                });
            }
            $this->info('Queuing forms: ' . join(',', $formIds));

            $config = new \stdClass();
            $config->studyId = $studyId;
            $config->useChoiceNames = true;
            $config->locale = $study->defaultLocale->id;
            $config->locale = "48984fbe-84d4-11e5-ba05-0800279114ca";

            foreach ($formIds as $formId){
                $reportId = Uuid::uuid4();
                array_push($remainingJobIds, $reportId);
                $reportJob = new FormReportJob($formId, $reportId, $config);
                $reportJob->handle();
                $this->info("Queued FormReportJob for form, $formId");
            }
        }

    }

}
