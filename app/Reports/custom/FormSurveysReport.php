<?php
/**
  * To run or modify this example, copy it into the "custom" directory.
  * Running this report from the command line: `php artisan trellis:run:report --name form_surveys --config="{ \"formId\": \"your form id\" }"`
  */

namespace App\Reports\custom;

use App\Reports\BaseReport;

class FormSurveysReport extends BaseReport {

  // This is the name used to identify the report in the user interface
  public $name = 'form_surveys';

  // This schema is used to validate properties passed as configuration to this report. See https://laravel.com/docs/5.8/validation#available-validation-rules for more info
  public $configSchema = [
    // Verify the formId is a string. Verify that this id exists in the form table
    'formId' => 'string|exists:form,id'
  ];

  // This method is called when the report runs
  public function handle ($config) {

    // Select all surveys for the configured formId. Must include the orderBy statement when streaming data
    $query = $this->DB()->table('survey')->where('form_id', $config['formId'])->orderBy('id');
    
    // Get the list of all columns in the survey table
    $headers = $this->DB()->getSchemaBuilder()->getColumnListing('survey');
    
    // Efficiently stream the results of this query to a CSV file
    $this->streamQuery($query, $headers);

  }

}