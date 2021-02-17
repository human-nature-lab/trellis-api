<?php

namespace App\Reports\core;

use App\Reports\BaseReport;

class StudyUsersReport extends BaseReport {

  public $name = "study_users";

  public function handle (array $config) {
    $headers = $this->tableColumns('user');
    $query = $this->DB()->table('user')->whereIn('id', function ($subquery) use ($config) {
      $subquery->select('user_id')->from('user_study')->where('study_id', $config['studyId']);
    })->orderBy('id');
    $this->streamQuery($query, $headers);
  }

}