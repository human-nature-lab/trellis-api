<?php

namespace App\Console\Commands;

use App\Library\FileHelper;
use Illuminate\Console\Command;
// use Illuminated\Console\WithoutOverlapping;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use App\Models\Snapshot;
use App\Models\Upload;
use Ramsey\Uuid\Uuid;
use DB;

class ImportUpload extends Command
{
    // USE: sudo -u www-data php artisan trellis:import:upload
    // use WithoutOverlapping;

    // TODO: Why doesn't this mutex strategy work?
    //protected $mutexStrategy = 'mysql';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trellis:import:upload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Check the uploads folder for unprocessed upload files and import any pending uploads.";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Starting import...");

        app()->configure('upload');   // save overhead by only loading config when needed

        $databaseName = env('DB_DATABASE');
        $this->info("databaseName: $databaseName");

        $uploadDirPath = FileHelper::storagePath(config('upload.directory.path'));
        $this->info("uploadDirPath: $uploadDirPath");

        // First check if there are pending uploads
        $pendingUploadCount = Upload::where('deleted_at', null)
            ->where('status', 'PENDING')
            ->count();

        $this->info("There $pendingUploadCount pending uploads.");

        if ($pendingUploadCount > 0) {
            $this->info("Pending uploads, starting upload processing.");
            // TODO
            $this->info("Done processing uploads.");
            return 0;
        }

        $this->info("No pending uploads.");
        return 0;
    }
}
