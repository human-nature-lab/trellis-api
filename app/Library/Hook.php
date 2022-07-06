<?php

namespace App\Library;

use Exception;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class Hook {
  private $script;
  private $binary = "bash";
  public function __construct(string $scriptPath) {
    $this->script = $scriptPath;
  }

  public function run() {
    $cwd = dirname($this->script);
    $script = basename($this->script);
    $args = [$this->binary, $script];
    Log::debug("cwd $cwd $script");
    // $args = ["echo", "test command"];
    $p = new Process($args, $cwd);
    $p->enableOutput();
    $code = $p->run();
    $output = $p->getOutput();
    $errOutput = $p->getErrorOutput();
    Log::debug("output $output $errOutput");
    Log::info("code $this->script $code");
    if ($code !== 0) {
      throw new Exception($errOutput);
    }
    return $code;
  }
}
