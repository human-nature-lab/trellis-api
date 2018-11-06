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
    protected $signature = 'trellis:bundle:reports {study} {--location=exports : The location to save the export at} {--name=reports.zip : The filename to use}';

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
        $studyId = $this->argument('study');
        $study = Study::where("id", "=", $studyId)->with("defaultLocale")->first();
        $types = ['geo', 'respondent', 'timing', 'interview', 'edge'];
        $formIds = Form::select('id')->whereNull('deleted_at')->where('is_published', true)->get()->map(function ($item) {
            return $item->id;
        });
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

        // Remove the existing reports if it's the default
        if($filename == "reports.zip" && file_exists($fullPath)) {
            unlink($fullPath);
        }

        $zip  = new ZipArchive();
        $zip->open(storage_path($fullPath), ZipArchive::OVERWRITE);
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
