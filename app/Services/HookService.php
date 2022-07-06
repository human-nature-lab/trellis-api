<?php

namespace App\Services;

class HookService {
  public function getDirScripts($dir) {
    $files = array_values(array_filter(scandir($dir), function ($file) use ($dir) {
      $p = $dir . '/' . $file;
      $file_parts = pathinfo($p);
      return !is_dir($p) && $file_parts['extension'] === 'sh';
    }));
    return array_map(function ($file) use ($dir) {
      return realpath($dir . '/' . $file);
    }, $files);
  }

  public function getPreSnapshotHooks() {
    $hookRoot = dirname(__FILE__) . '/../../hooks/PreSnapshot';
    return $this->getDirScripts($hookRoot);
  }

  public function getPostSnapshotHooks() {
    $hookRoot = dirname(__FILE__) . '/../../hooks/PostSnapshot';
    return $this->getDirScripts($hookRoot);
  }
}
