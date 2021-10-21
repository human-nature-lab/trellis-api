<?php
/**
  * To run or modify this example, copy it into the "custom" directory.
  * Running this report from the command line: `php artisan trellis:run:report --name multiple_tables`
  */

namespace App\Reports\custom;

use App\Reports\BaseReport;

class MultipleTablesReport extends BaseReport {

  // This is the name used to identify the report in the user interface
  public $name = 'multiple_tables';

  // This method is called when the report runs
  public function handle ($config) {

    // ALl of the tables we want to export in this report
    $tables = ['geo', 'respondent', 'respondent_geo', 'respondent_name', 'translation', 'translation_text'];

    foreach($tables as $table) {
      $this->streamTable($table);
    }

  }

}