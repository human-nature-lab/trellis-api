<?php
/**
  * To run or modify this example, copy it into the "custom" directory.
  * Running this report from the command line: `php artisan trellis:run:report --name simplest_report`
  */

namespace App\Reports;

class SimplestReport extends BaseReport {

  // This is the name used to identify the report in the user interface
  public $name = 'simplest_report';

  // This method is called when the report runs
  public function handle ($config) {

    // Simply stream a table to a CSV file.
    $this->streamTable('user');

  }

}