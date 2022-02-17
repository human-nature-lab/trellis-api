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
    protected $signature = 'trellis:download-app {--asset-name=trellis-web.zip} {--timeout=120}';

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
        $assetName = $this->option('asset-name');
        $timeout = $this->option('timeout');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Trellis API');
        curl_setopt($ch, CURLOPT_URL, 'https://api.github.com/repos/human-nature-lab/trellis-app/releases?prerelease=true');
        $result = curl_exec($ch);
        curl_close($ch);
        $versions = json_decode($result, true);
        $versions = array_filter($versions, function ($v) use ($assetName) {
          if (!isset($v['tag_name']) || !isset($v['assets'])) return false;
          $filteredAssets = array_filter($v['assets'], function ($a) use ($assetName) {
            return $a['name'] === $assetName;
          });
          $webAsset = array_pop($filteredAssets);
          return isset($webAsset);
        });
        $choices = array_map(function ($v) {
          return $v['tag_name'];
        }, $versions);


        $chosenVersionName = $this->choice("Which version of the Trellis app do you want to download and install?", $choices, 0);
        $this->info($chosenVersionName);
        $chosenVersions = array_filter($versions, function($v) use ($chosenVersionName) { return $v["tag_name"] == $chosenVersionName; });
        $chosenVersion = array_pop($chosenVersions);
        
        $chosenAsset = array_values(array_filter($chosenVersion['assets'], function ($a) use ($assetName) {
          return $a['name'] === $assetName;
        }))[0];
        $chosenVersionUrl = $chosenAsset['browser_download_url'];
        $this->info($chosenVersionUrl);
        
        $zipPath = storage_path('temp/trellis-web.zip');
        $this->info("Downloading to $zipPath...");
        
        $ch = curl_init( $chosenVersionUrl);
        $fp = fopen($zipPath, 'w+');

        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $bar = $this->output->createProgressBar(100);
        $bar->setProgress(0);
        curl_setopt($ch, CURLOPT_NOPROGRESS, false);
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, function ($resource, $downloadSize, $downloaded, $uploadSize, $uploaded) use ($bar) {
            $this->progress($downloadSize, $downloaded, $bar);
        });

        curl_exec($ch);

        if(curl_errno($ch)) {
            $this->error(curl_error($ch));
        }

        $bar->finish();

        curl_close($ch);
        fclose($fp);

        $trellisAppDir = $this->ask('Where do you want to install the trellis web application?', '/var/www/trellis-app');

        $this->info("Extracting \"$chosenVersionName\" to \"$trellisAppDir\"...");

        $zip = new ZipArchive;
        $res = $zip->open($zipPath);
        if ($res === TRUE) {
          $zip->extractTo($trellisAppDir);
        } else {
          $this->error("Unable to open zip archive.");
          return;
        }

        unlink($zipPath);

        $this->info("");
        $apiEndpoint = $this->ask("What is the URL of your server's API endpoint? (e.g. https://api.yourdomainname.com)");
        $this->info("Writing APP config file...");

        $configFile = "window.config = {
  appEnv: 'WEB',
  apiRoot: '$apiEndpoint'
}\n";

        file_put_contents($trellisAppDir . '/www/config.js', $configFile);

        $this->info("\nDone!");
        return 0;
    }

    private function progress($downloadSize, $downloaded, $bar) {
        if ($downloadSize > 0) {
            $bar->setProgress(($downloaded/$downloadSize) * 100);
        }
    }
}
