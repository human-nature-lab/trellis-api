<?php

namespace App\Console\Commands;

use App\Jobs\EdgeReportJob;
use App\Jobs\FormReportJob;
use App\Jobs\GeoReportJob;
use App\Jobs\InterviewReportJob;
use App\Jobs\RespondentReportJob;
use App\Jobs\TimingReportJob;
use App\Models\Report;
use App\Models\Study;
use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\Job;
use Laravel\Lumen\Routing\DispatchesJobs;
use Log;
use Queue;
use Ramsey\Uuid\Uuid;

class MakeReports extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trellis:make:reports';

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
        $studyId = 'ad9a9086-8f15-4830-941d-416b59639c41';
        $study = Study::where("id", "=", $studyId)->with("defaultLocale")->first();
        $mainJobConstructors = [InterviewReportJob::class, EdgeReportJob::class, GeoReportJob::class, TimingReportJob::class, RespondentReportJob::class];

        foreach ($mainJobConstructors as $constructor){
            $reportId = Uuid::uuid4();
            array_push($remainingJobIds, $reportId);
            $reportJob = new $constructor($studyId, $reportId, new \stdClass());
            $this->dispatch($reportJob);
            $this->info("Queued $constructor");
        }

        $formIds = [
            '5612115f-9208-4696-9497-4398ae112f8b',
            '03551748-f180-44fa-9d58-c6b720c095e9',
            'be587a4a-38c6-46cb-a787-1fcb4813b274',
            '4eac1508-0643-4a12-ac5c-f88e5523b9b4',
            '363cc222-c84b-411b-be55-8c5cb3d20ad1',
            '5826ca44-39a5-49cb-ae6d-779d0e9acfe7',
            '310bf97e-df3d-4ec9-bed0-1c970984f817',
            'a3a1386d-ebb0-4c3e-a72c-393e538abcd6',
        ];

        $config = new \stdClass();
        $config->studyId = $studyId;
        $config->useChoiceNames = true;
        $config->locale = $study->defaultLocale->id;
        $config->locale = "48984fbe-84d4-11e5-ba05-0800279114ca";

        foreach ($formIds as $formId){
            $reportId = Uuid::uuid4();
            array_push($remainingJobIds, $reportId);
            $reportJob = new FormReportJob($formId, $reportId, $config);
            $this->dispatch($reportJob);
            $this->info("Queued FormReportJob for form, $formId");
        }


        // Poll the Reports to check if they have completed
        $pollCount = 0;
        $totalJobs = count($mainJobConstructors) + count($formIds);
        $bar = $this->output->createProgressBar($totalJobs);
        $bar->setFormatDefinition("detailed", ' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');
        $bar->setFormat("detailed");
        while(true){
            sleep(5);
            for($i=0; $i < count($remainingJobIds); $i++){
                $id = $remainingJobIds[$i];
                if(Report::find($id)->status == "saved"){
                    array_splice($remainingJobIds, $i, 1);
                    $i --;
                    $bar->advance();
                }
            }

            if(count($remainingJobIds) <= 0){
                $bar->finish();
                $this->info("\nAll jobs have completed successfully!");
                break;
            }

            if($pollCount > 200){
                $jobCount = Job::count();
                $this->error("\nGenerating the reports took too long. $jobCount / $totalJobs jobs have not completed.");
                return;
            }

            $pollCount ++;
            $c = count($remainingJobIds);
            $bar->setMessage("$c / $totalJobs jobs have completed...");
        }


    }

}
