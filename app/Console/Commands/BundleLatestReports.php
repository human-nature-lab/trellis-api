<?php

namespace App\Console\Commands;

use App\Models\Form;
use App\Models\Report;
use App\Models\Study;
use Illuminate\Console\Command;
use ZipArchive;

class BundleLatestReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trellis:bundle:reports {--location=exports : The location to save the export at} {--name=reports.zip : The filename to use}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Take all of the latest reports and put them in a zip file with the correct formatting.';

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
        $studyId = 'ad9a9086-8f15-4830-941d-416b59639c41';
        $study = Study::where("id", "=", $studyId)->with("defaultLocale")->first();
        $types = ['geo', 'respondent', 'timing', 'interview', 'edge'];
        $formIds = [
            '5612115f-9208-4696-9497-4398ae112f8b',
            '03551748-f180-44fa-9d58-c6b720c095e9',
            'be587a4a-38c6-46cb-a787-1fcb4813b274',
            '750e24c9-3a9c-462f-bcc1-17f197d6701f',
            '363cc222-c84b-411b-be55-8c5cb3d20ad1',
            '5826ca44-39a5-49cb-ae6d-779d0e9acfe7',
            '310bf97e-df3d-4ec9-bed0-1c970984f817',
            'a3a1386d-ebb0-4c3e-a72c-393e538abcd6',
        ];
        $reports = Report::whereIn("report.type", $types)
            ->where("report.report_id", '=', $study->id)
            ->orderBy('report.updated_at', 'desc')
            ->limit(count($types))
            ->with("files")
            ->distinct()
            ->get();
        $formReports = Report::whereIn("report.report_id", $formIds)
            ->where("report.type", "=", "form")
            ->orderBy('report.updated_at', 'desc')
            ->limit(count($formIds))
            ->with("files")
            ->get();


        // Save the files in a zip archive
        $saveDir = $this->option('location');
        $filename = $this->option("name");
        $fullPath = $saveDir . "/" . $filename;

//        if(!file_exists($saveDir)) {
//            $this->info("Directory doesn't exist. Making directory at ". $saveDir);
//            try {
//                mkdir($saveDir, 0755, true);
//            } catch(Exception $e){
//                $this->error("Unable to make directory");
//                $this->error($e->getTraceAsString());
//                return;
//            }
//        }

        if(file_exists($fullPath)){
           $this->error("File already exists! Delete or change filename to continue.");
           return;
        }

        $zip  = new \ZipArchive();
        $zip->open(storage_path($fullPath), 1);
        foreach($reports as $report){
            foreach($report->files as $file) {
                $zipName = $study->name . '_' . $report->type . '_export.csv';
                $this->info("Adding ".$zipName." to the archive");
                $zip->addFile(storage_path("app/".$file->file_name), $zipName);
            }
        }

        foreach($formReports as $report){
            $form = Form::with("nameTranslation")
                ->find($report->report_id);
            foreach($report->files as $file){
                $nameTranslation = $form->nameTranslation;
                $formName = $nameTranslation->translationText[0]->translated_text;
                $zipName = $file->file_type."/".$formName.'_'.$file->file_type.'_export.csv';
                $this->info("Adding ".$zipName." to the archive");
                $zip->addFile(storage_path("app/".$file->file_name), $zipName);
            }
        }


        $zip->close();
        $this->info("Finished writing all reports to the archive");

    }

}
