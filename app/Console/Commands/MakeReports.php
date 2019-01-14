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
use Queue;
use Ramsey\Uuid\Uuid;

class MakeReports extends Command
{

    /*
    use DispatchesJobs, AutoDispatch {
        AutoDispatch::dispatch insteadof DispatchesJobs;
    }
    */

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trellis:make:reports {study} {--only-forms}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run each type of report once to get the latest data';

    /**
     * Execute the console command.
     *
     * To call from within code:
     *
     * ob_start();
     *
     * \Illuminate\Support\Facades\Artisan::call('trellis:check:models');
     *
     * $result = json_decode(ob_get_clean(), true);
     *
     * @return mixed
     */
    public function handle()
    {

        Queue::after(function ($connection, $job, $data) {
            Log::debug("Finished job: ", $job->id, $data);
        });

        $remainingJobIds = [];
        $studyId = $this->argument('study');
        $study = Study::where("id", "=", $studyId)->with("defaultLocale")->first();
        if (!isset($study)) {
            throw Error('Study id must be valid');
        }
        $mainJobConstructors = [TimingReportJob::class, RespondentGeoJob::class, InterviewReportJob::class, EdgeReportJob::class, GeoReportJob::class, ActionReportJob::class, RespondentReportJob::class];

        if (!$this->option('only-forms')) {
            foreach ($mainJobConstructors as $constructor){
                $reportId = Uuid::uuid4();
                array_push($remainingJobIds, $reportId);
                $reportJob = new $constructor($studyId, $reportId, new \stdClass());
                $reportJob->handle();
//            dispatch($reportJob);
                $this->info("Queued $constructor");
            }
        }

        $formIds = Form::select('id')->whereIn('id', function ($q) use ($studyId) {
            $q->select('form_master_id')->from('study_form')->where('study_id', $studyId);
        })->whereNull('deleted_at')->where('is_published', true)->get()->map(function ($item) {
            return $item->id;
        });

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
//            dispatch($reportJob);
            $this->info("Queued FormReportJob for form, $formId");
        }


        // Poll the Reports to check if they have completed
//        $pollCount = 0;
//        $totalJobs = count($mainJobConstructors) + count($formIds);
//        $bar = $this->output->createProgressBar($totalJobs);
//        $bar->setFormatDefinition("detailed", ' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');
//        $bar->setFormat("detailed");
//        while(true){
//            sleep(5);
//            for($i=0; $i < count($remainingJobIds); $i++){
//                $id = $remainingJobIds[$i];
//                if(Report::find($id)->status == "saved"){
//                    array_splice($remainingJobIds, $i, 1);
//                    $i --;
//                    $bar->advance();
//                }
//            }
//
//            if(count($remainingJobIds) <= 0){
//                $bar->finish();
//                $this->info("\nAll jobs have completed successfully!");
//                break;
//            }
//
//            if($pollCount > 200){
//                $jobCount = Job::count();
//                $this->error("\nGenerating the reports took too long. $jobCount / $totalJobs jobs have not completed.");
//                return;
//            }
//
//            $pollCount ++;
//            $c = count($remainingJobIds);
//            $bar->setMessage("$c / $totalJobs jobs have completed...");
//        }


    }

}
