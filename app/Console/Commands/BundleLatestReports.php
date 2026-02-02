<?php

namespace App\Console\Commands;

use App\Models\Form;
use App\Models\Report;
use App\Models\Study;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class BundleLatestReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trellis:bundle:reports {study?} 
      {--unpublished : Include unpublished forms}
      {--location=exports : The location to save the export at} 
      {--name=reports.zip : The filename to use}
      {--exclude-form-timing : Exclude the timing report from the form reports}
      {--study= : The study to bundle reports for}';

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
    public function handle () {
      set_time_limit(0);
        $studyId = $this->getStudyId();
        $study = Study::where("id", "=", $studyId)->with("defaultLocale")->first();
        $types = ['respondent_geo', 'geo', 'respondent', 'timing', 'interview', 'edge', 'action', 'asset'];
        $formQuery = Form::select('id')->
          whereIn('form_master_id', function ($q) use ($studyId) {
              $q->select('form_master_id')->from('study_form')->where('study_id', $studyId);
          })->
          whereNull('deleted_at');
        if (!$this->option('unpublished')) {
            $formQuery->where('is_published', 1);
        }

        $formIds = $formQuery->
          get()->
          map(function ($item) {
              return $item->id;
          });

        $reports = new Collection();
        $formReports = new Collection();

        foreach ($types as $type) {
            $report = Report::where('type', '=', $type)
                ->where('study_id', '=', $studyId)
                ->where('status', '=', "saved")
                ->with('files')
                ->orderBy('updated_at', 'desc')
                ->first();
            if (isset($report)) {
                $reports->push($report);
            }
        }

        foreach ($formIds as $formId) {
            $report = Report::where('type', '=', 'form')
                ->where('form_id', '=', $formId)
                ->with('files')
                ->orderBy('updated_at', 'desc')
                ->first();
            if (isset($report)) {
                $formReports->push($report);
            }
        }


        // Save the files in a zip archive
        $saveDir = $this->option('location');
        $filename = $this->option("name");
        $fullPath = $saveDir . "/" . $filename;

        // Remove the existing reports if it's the default
        if(file_exists(storage_path($fullPath))) {
            $this->info("removing existing reports at $fullPath");
            unlink(storage_path($fullPath));
        }

        $zip  = new ZipArchive();
        $zip->open(storage_path($fullPath), ZipArchive::CREATE|ZipArchive::OVERWRITE);
        foreach($reports as $report){
          foreach($report->files as $file) {
            $zipName = $study->name . '_' . $report->type . '_export.csv';
            $this->info("Adding ".$zipName." to the archive");
            $realPath = storage_path("app/".$file->file_name);
            $zip->addFile($realPath, $zipName);
          }
        }

        foreach($formReports as $report){
            $form = Form::with("nameTranslation")
                ->find($report->form_id);
            foreach($report->files as $file){
                if ($this->option('exclude-form-timing') && $file->file_type === 'timing') {
                    continue;
                }
                $nameTranslation = $form->nameTranslation;
                $formName = $nameTranslation->translationText[0]->translated_text;
                $zipName = "forms/$formName/v$form->version/$file->file_type/$file->file_type"."_export.csv";
                $this->info("Adding $zipName to the archive");
                $zip->addFile(storage_path("app/$file->file_name"), $zipName);
            }
        }

        $zip->close();
        $this->info("Finished writing all reports to the archive");

    }


    private function getStudyId () {
      $studyId = $this->option('study');
      if (!$studyId) {
        $studyId = $this->argument('study');
      }
      if (!$studyId) {
        $studies = Study::all();
        foreach ($studies as $study) {
          $this->info("Study: " . $study->name . " (" . $study->id . ")");
        }
        throw new \Exception("No study id provided");
      }
      return $studyId;
    }

}
