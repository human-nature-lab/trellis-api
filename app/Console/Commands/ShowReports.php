<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Reports\ReportRunner;
use DB;
use Illuminate\Database\Eloquent\Collection;

class ShowReports extends Command {

    protected $signature = 'trellis:report:show 
                              { --location : Show the file location for each report }';

    protected $description = 'Show the available reports on this server';

    public function handle () {
      $showLocation = $this->option('location');
      $reports = ReportRunner::allReports();
      $headers = ['Name', 'Config'];
      if ($showLocation) {
        array_push($headers, 'Location');
      }
      $this->table(
        $headers,
        (new Collection($reports))->map(function ($r) use ($showLocation) {
          $vals = [
            $r['name'],
            json_encode($r['configSchema'])
          ];
          if ($showLocation) {
            array_push($vals, $r['filename']);
          }
          return $vals;
        })
      );
    }
}
