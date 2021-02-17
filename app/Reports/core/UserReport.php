<?php

namespace App\Reports\core;

use App\Reports\BaseReport;

class UserReport extends BaseReport {

  public $name = "user_table";

  public function handle ($config) {
    $this->streamTable('user');
  }

}