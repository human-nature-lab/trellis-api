<?php

namespace App\Reports;

class UserReport extends BaseReport {

  public $name = "user_table";

  public function handle ($config) {
    $this->streamTable('user');
  }

}