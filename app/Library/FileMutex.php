<?php

namespace App\Library;

use Illuminate\Support\Carbon;

class FileMutex {

  private $f;
  public  $loc;
  public  $timeoutMs;

  public function __construct (string $loc, int $timeoutMs = 60 * 1000) {
    $this->loc = $loc;
    $this->timeoutMs = $timeoutMs;
  }

  public function lock () {
    if ($this->isLocked()) {
      return false;
    } else {
      $this->f = fopen($this->loc, 'w');
      if (!$this->f) return false;
      if (flock($this->f, LOCK_EX | LOCK_NB)) {
        $wrote = fwrite($this->f, json_encode([
          'locked_at' => Carbon::now(),
          'release_at' => Carbon::now()->addMillis($this->timeoutMs),
        ]));
        if (!$wrote) {
          flock($this->f, LOCK_UN);
          fclose($this->f);
          $this->f = false;
          return false;
        }
        return true;
      } else {
        return false;
      }
    }
  }

  public function unlock () {
    if ($this->f) {
      ftruncate($this->f, 0);
      $unlocked = flock($this->f, LOCK_UN);
      fclose($this->f);
      return $unlocked;
    }
    return false;
  }

  public function isLocked () {
    if ($this->f) {
      return true;
    }
    $f = fopen($this->loc, 'w');
    $isNotLocked = flock($f, LOCK_EX | LOCK_NB);
    if ($isNotLocked) {
      flock($f, LOCK_UN);
    }
    fclose($f);
    return !$isNotLocked;
  }

  public function do ($cb) {
    $locked = false;
    try {
      $locked = $this->lock();
      if ($locked) {
        return $cb();
      } else {
        throw new \Exception('unable to aquire lock');
      }
    } finally {
      if ($locked && !$this->unlock()) {
        throw new \Exception('failed to unlock');
      }
    }
  }

}