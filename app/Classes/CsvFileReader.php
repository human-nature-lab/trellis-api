<?php

namespace App\Classes;

use Error;

class CsvFileReader {

  private $file;
  private $path;
  private $headers;
  public $isOpen = false;
  public $makePath;

  public function __construct ($path, $makePath = false, $headers = null) {
    $this->path = $path;
    $this->headers = $headers;
    $this->makePath = $makePath;
  }


  public function open () {

    $existing = file_exists(dirname($this->path));
    if (!$existing) {
      if ($this->makePath) {
        mkdir(dirname($this->filePath), 0777, true);
      } else {
        throw new Error('This file does not exist');
      }
    }

    $this->file = fopen($this->path, 'r');

    if (!isset($this->headers)) {
      $row = fgetcsv($this->file);
      $this->headers = $row;
    }

    $this->isOpen = true;

  }

  public function close () {

    if (isset($this->file)) {
      fclose($this->file);
    }

    $this->isOpen = false;
  }

  public function getNextRowHash () {

    $raw = $this->getNextRow();

    if (!$raw) return $raw;

    $row = [];

    foreach ($this->headers as $i => $key) {
      $row[$key] = $raw[$i];
    }

    return $row;
  }

  public function getNextRow () {

    if (!$this->isOpen) throw new Error('This file has not been opened');

    return fgetcsv($this->file);

  }

}