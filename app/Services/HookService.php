<?php

namespace App\Services;

use App\Library\Hook;
use Exception;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Yaml\Yaml;

class HookService {

  public function getHooksDef () {
    $names = ['hooks.json', 'hooks.yaml'];
    $path = '';
    foreach($names as $name) {
      $p = dirname(__FILE__) . '/../../' . $name;
      Log::info("checking for hooks at $p");
      if (file_exists($p)) {
        $path = $p;
        break;
      }
    }
    if ($path === '') {
      return [];
    }
    $d = file_get_contents($path);
    $res = Yaml::parse($d);
    foreach($res as $key => $val) {
      if (!in_array($key, ['preSnapshot', 'postSnapshot', 'geo', 'respondent'])) {
        throw new Exception("invalid key in hooks config: $key");
      }
    }
    return $res;
  }

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
    $files = $this->getDirScripts($hookRoot);
    $hooks = [];
    foreach ($files as $file) {
      $hooks[] = new Hook('bash', [$file]);
    }
    return $hooks;
  }

  public function getPostSnapshotHooks() {
    $hookRoot = dirname(__FILE__) . '/../../hooks/PostSnapshot';
    $files = $this->getDirScripts($hookRoot);
    $hooks = [];
    foreach ($files as $file) {
      $hooks[] = new Hook('bash', [$file]);
    }
    return $hooks;
  }

  public function getRespondentHooks() {
    $hooks = $this->getHooksDef();
    $results = [];
    if (isset($hooks['respondent'])) {
      $respondentHooks = $hooks['respondent'];
      foreach ($respondentHooks as $def) {
        if (!isset($def['id'])) {
          throw new Exception("respondent hook must have an id");
        } else if (!isset($def['bin'])) {
          throw new Exception("respondent hook must have a bin");
        }
        $hook = new Hook($def['bin'], $def['args']);
        $hook->def = $def;
        $results[$def['id']] = $hook;
      }
    }
    return $results;
  }

  public function getGeoHooks() {
    $hooks = $this->getHooksDef();
    $results = [];
    if (isset($hooks['geo'])) {
      $geoHooks = $hooks['geo'];
      foreach ($geoHooks as $def) {
        if (!isset($def['id'])) {
          throw new Exception("geo hook must have an id");
        } else if (!isset($def['bin'])) {
          throw new Exception("geo hook must have a bin");
        }

        $hook = new Hook($def['bin']);
        if (isset($def['args'])) {
          $hook->args = $def['args'];
        }
        // if (isset($def['env'])) {
        //   $hook->setEnv($def['env']);
        // }
        if (isset($def['cwd'])) {
          $hook->cwd = $def['cwd'];
        }
        $hook->def = $def;
        $results[$def['id']] = $hook;
      }
    }
    return $results;
  }
}
