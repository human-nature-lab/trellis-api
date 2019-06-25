<?php

namespace App\Library;

class ShellHelper {

  public static function commandExists (String $cmd): bool {
    $return = shell_exec(sprintf("which %s", escapeshellarg($cmd)));
    return !empty($return);
  }

  public static function getBestAvailableAwk (): String {
    $preference = ['mawk', 'gawk', 'awk'];
    foreach ($preference as $awk) {
      if (self::commandExists($awk)) {
        return $awk;
      }
    }
    throw new \Exception("No awk command exists on this system");
  }

}