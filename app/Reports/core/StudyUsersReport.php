<?php

namespace App\Reports;

class StudyUsersReport extends Base {

  public $name = "study_users";

  public function handle ($config) {
    $headers = $this->tableColumns('user');
    $query = $this->DB()->table('user')->whereIn('id', function ($subquery) {
      $subquery->select('user_id')->from('user_study')->where('study_id', $this->studyId);
    })->orderBy('id');
    $this->streamQuery($query, $headers);
  }

}