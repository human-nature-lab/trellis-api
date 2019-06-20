<?php

namespace App\Jobs;


use App\Library\CsvFileWriter;
use App\Library\QueryHelper;
use App\Library\ShellHelper;
use App\Models\Action;
use App\Services\ReportService;
use Illuminate\Support\Facades\Schema;
use Log;
use App\Models\Report;
use App\Models\Datum;
use App\Models\Study;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


class ActionReportJob extends Job {

    protected $studyId;
    protected $report;
    private $file;
    const TSV_TO_CSV = 'app/Console/Scripts/tsv-to-csv.awk';

    public function __construct($studyId, $fileId)
    {
        Log::debug("ActionReportJob - constructing: $studyId");
        $this->studyId = $studyId;
        $this->report = new Report();
        $this->report->id = $fileId;
        $this->report->type = 'action';
        $this->report->status = 'queued';
        $this->report->report_id = $this->studyId;
        $this->report->save();
    }

    public function handle()
    {
        set_time_limit(0);
        $startTime = microtime(true);
        Log::debug("ActionReportJob - handling: $this->studyId, $this->report->id");
        try{
            $this->create();
            $this->report->status = 'saved';
        } catch(Exception $e){
            $this->report->status = 'failed';
            $duration = microtime(true) - $startTime;
            Log::debug("ActionReportJob - failed: $this->studyId after $duration seconds");
        } finally{
            $this->report->save();
            if (isset($this->file)) {
                $this->file->close();
            }
            $duration = microtime(true) - $startTime;
            Log::debug("ActionReportJob - finished: $this->studyId in $duration seconds");
        }
    }


    public function create(){

        $id = Uuid::uuid4();
        $fileName = $id . '.csv';
        $filePath = storage_path('app/' . $fileName);

        $sql = "select a.*, i.survey_id from action a inner join interview i on i.id = a.interview_id inner join survey s on i.survey_id = s.id where s.study_id = ?";

        $tsv2CsvPath =  base_path() . '/' . self::TSV_TO_CSV;

        $statement = QueryHelper::preparedSql($sql, [$this->studyId]);
        $statement = str_replace('"', '\\"', $statement);

        $dbHost = env('DB_HOST');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');
        $dbDatabase = env('DB_DATABASE');

        $bestAwk = ShellHelper::getBestAvailableAwk();

        $cmd = "mysql -u$dbUser -p$dbPass -h$dbHost -B -e\"$statement\" $dbDatabase | $bestAwk -f $tsv2CsvPath > $filePath";
        $process = Process::fromShellCommandline($cmd, base_path(), [
          'DB_HOST' => env('DB_HOST'),
          'DB_PORT' => env('DB_PORT'),
          'DB_USERNAME' => env('DB_USERNAME'),
          'MYSQL_PWD' => env('DB_PASSWORD'),  // use MYSQL_PWD to suppress "mysqldump: [Warning] Using a password on the command line interface can be insecure." instead of passing --password="$DB_PASSWORD"  //BUG decide whether to use mysql_config_editor
          'DB_DATABASE' => env('DB_DATABASE'),
        ]);

        $process->setTimeout(null)->run();

        if (!$process->isSuccessful()) {
          throw new ProcessFailedException($process);
        }

        ReportService::saveFileStream($this->report, $fileName);

    }
}