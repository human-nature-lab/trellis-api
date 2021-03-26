<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BaseCommand extends Command {

  private $isCli = false;

  function __construct () {
    parent::__construct();
    // call_user_func_array(array($this, 'parent::__construct'), func_get_args());
    $this->isCli = strpos(php_sapi_name(), 'cli') !== false;
  }

  public function info($string, $verbosity = null) { 
    if ($this->isCli) {
      parent::info($string, $verbosity);
    } else {
      Log::info($string);
    }
  }

  public function error($string, $verbosity = null) {
    if ($this->isCli) {
      parent::error($string, $verbosity);
    } else {
      Log::error($string);
    }
  }

  public function time(string $msg, $cb) {
    $this->info("Start $msg");
    $start = microtime(true);
    $res = $cb();
    $duration = microtime(true) - $start;
    $this->info("Completed $msg in $duration seconds");
    return $res;
  }

}