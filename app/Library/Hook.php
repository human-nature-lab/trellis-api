<?php

namespace App\Library;

use Exception;
use Symfony\Component\Process\Process;

class Hook {
  public $args;
  public $def;
  private $binary;
  private $env = [];
  private $p;
  public $cwd;
  public function __construct(string $binary = "bash", $args = [], string $cwd = null) {
    $this->binary = $binary;
    $this->args = $args;
    $this->cwd = $cwd;
    if (is_null($this->cwd)) {
      $this->cwd = dirname(__FILE__) . '/../../';
    }
  }

  public function setEnv(string $key, string $val) {
    $this->env[$key] = $val;
  }

  public function setInput (string $val) {
    $this->p->setInput($val);
  }

  public function setup($args = null) {
    if (isset($args)) {
      $args = array_merge([$this->binary], $args);
    } else {
      $args = [$this->binary];
    }
    $args = array_merge($args, $this->args);
    $env = array_merge(getEnv(), $this->env);
    $this->p = new Process($args, $this->cwd, $env);
    return $this;
  }

  public function start () {
    $this->p->enableOutput();
    $this->p->start();
  }

  public function wait () {
    $code = $this->p->wait();
    $output = $this->p->getOutput();
    $errOutput = $this->p->getErrorOutput();
    if ($code !== 0) {
      throw new Exception($errOutput);
    }
    return $output;
  }

  public function run () {
    $this->start();
    return $this->wait();
  }

}
