<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ZipArchive;

class DownloadApp extends Command
{
    // USE: sudo -u www-data php artisan trellis:download-app

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trellis:download-app';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This command will display a list of available trellis-app distributions to the user and 
    download and install the selected version.";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /* First, let's get the RELEASES.md from github and parse the list of releases. */
        $this->info("Checking for trellis app releases...");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://raw.githubusercontent.com/human-nature-lab/trellis/master/RELEASES.md");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        /* Get everything up to the --- horizontal rule */
        $versionListString = trim(substr($output, 0, strpos($output, "---")));
        $versionList = explode("\n", $versionListString);
        $versions = array();
        $choices = array();
        foreach ($versionList as $versionString) {
            $version = array();
            $start = strpos($versionString, "[") + 1;
            $end = strpos($versionString, "]");
            $version["name"] = substr($versionString, $start, $end - $start);
            $start = strpos($versionString, "(") + 1;
            $end = strpos($versionString, ")");
            $version["url"] = substr($versionString, $start, $end - $start);
            $choices[] = $version["name"];
            $versions[] = $version;
        }
        $chosenVersionName = $this->choice("Which version of the Trellis app do you want to download and install?", $choices, (count($choices) - 1));
        $chosenVersion = array_values(array_filter($versions, function($v) use ($chosenVersionName) { return $v["name"] == $chosenVersionName; }));

        $this->info($chosenVersion[0]["url"]);
        curl_setopt($ch, CURLOPT_URL, $chosenVersion[0]["url"]);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $bar = $this->output->createProgressBar(100);
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, function ($resource, $download_size, $downloaded, $upload_size, $uploaded) use ($bar) {
          $bar->setProgress($downloaded / $download_size  * 100);
          ob_flush();
          flush();
          sleep(1); // just to see effect
        });

        $rawFile = curl_exec($ch);

        if(curl_errno($ch)) {
            $this->error(curl_error($ch));
        }

        $bar->finish();

        $zipPath = storage_path('temp/trellis-app.zip');
        file_put_contents($zipPath, $rawFile);

        $trellisAppDir = $this->anticipate('Where do you want to install the trellis app?', ['/var/www/trellis-app']);

        $zip = new ZipArchive;
        $res = $zip->open($zipPath);
        if ($res === TRUE) {
            $zip->extractTo($trellisAppDir);
        } else {
            $this->error("Unable to open zip archive.");
        }

        unlink($zipPath);

        curl_close($ch);
        $this->info('Done!');
        return 0;
    }
}
