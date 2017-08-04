<?php

namespace App\Console\Commands;

use App\Library\DatabaseHelper;
use App\Library\FileHelper;
use App\Library\TimeHelper;
use App\Models\Log;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminated\Console\WithoutOverlapping;
use PhpMyAdmin\SqlParser\Parser;
use PhpMyAdmin\SqlParser\Statements\InsertStatement;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ImportSnapshot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trellis:import:snapshot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports snapshot from stdin.  Returns either 1) the number of rows modified and exit code 0 or 2) the output of `php artisan trellis:check:mysql:foreignkeys` and exit code 1';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $process = new Process('gunzip | php artisan trellis:import:sqlite', base_path());

        $exitCode = $process->setInput(fopen(\App::runningInConsole() ? 'php://stdin' : 'php://input', 'rb'))->setTimeout(null)->run();

        echo $process->getOutput();

        return $exitCode;
    }
}
