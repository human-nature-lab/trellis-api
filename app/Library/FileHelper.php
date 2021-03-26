<?php

namespace App\Library;

use DirectoryIterator;
use FilesystemIterator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

// define STDIN, STDOUT and STDERR if not defined (due to not running in CLI)

if (!defined('STDIN')) {
  define('STDIN', fopen('php://stdin', 'r'));
}

if (!defined('STDOUT')) {
  define('STDOUT', fopen('php://stdout', 'w'));
}

if (!defined('STDERR')) {
  define('STDERR', fopen('php://stderr', 'w'));
}

class FileHelper {
  /**
   * Similar to realpath() but works if file doesn't exist.
   *
   * @var string $path
   * @return string
   */
  public static function normalizePath($path) {
    $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
    $absolutes = [];

    foreach ($parts as $part) {
      if ('.' == $part) {
        continue;
      }

      if ('..' == $part) {
        array_pop($absolutes);
      } else {
        $absolutes[] = $part;
      }
    }

    return implode(DIRECTORY_SEPARATOR, $absolutes);
  }

  /**
   * Similar to storage_path() but prevents returning path outside of storage directory.
   *
   * @var string $path
   * @return string
   */
  public static function storagePath($path) {
    return storage_path(static::normalizePath($path));
  }

  /**
   * Similar to mkdir() but automatically creates directories recursively and enables read/write permissions for user and group.
   *
   * @var string $path
   * @return string
   */
  public static function mkdir($path) {
    try {
      return mkdir($path, 0770, true);   // try creating directory, suppress error if it already exists
    } catch (\Exception $e) {
      return -1;
    }
  }

  /**
   * Similar to Illuminate\Filesystem\Filesystem::cleanDirectory() but allows specifying maximum size in bytes (over which older files will be deleted until satisfied).
   *
   * Defaults to sorting by modification time ascending, so oldest files at the front of the list are deleted first.
   *
   * @param  string  $directory  Path to directory
   * @param  int  $size  Maximum size of directory after cleaning
   * @param  string  $sort  FilesystemIterator method starting with 'get' or 'is' like 'getMTime', 'getBasename', 'isDir', etc
   * @param  bool  $ascending  True or false
   * @return bool
   */
  public static function cleanDirectory($directory, $size = 0, $sort = 'getMTime', $ascending = true) {
    if (strpos($sort, 'get') !== 0 || strpos($sort, 'is')) {
      throw new \Exception(__METHOD__ . ' expects $sort to start with \'get\' or \'is\'');
    }

    while (true) {
      $totalSize = 0;
      $bestValue = $ascending ? INF : -INF;
      $bestFile = null;

      foreach (new \FilesystemIterator($directory) as $file) {
        $totalSize += $file->getSize();

        $value = $file->{$sort}();

        if ($ascending ? ($bestValue > $value) : ($bestValue < $value)) {
          $bestValue = $value;
          $bestFile = $file;
        }
      }

      if ($totalSize > $size) {
        $path = $bestFile->getPathname();

        if ($bestFile->isDir()) {
          (new Filesystem)->deleteDirectory($path);
        } else {
          unlink($path);
        }
      } else {
        break;
      }
    }
  }

  public static function isDotFile (string $path): bool {
    $filename = basename($path);
    return substr($filename, 0, 1) === '.';
  }

  public static function removeOldFiles (string $dirPath, int $maxAgeS): array {
    if (!file_exists($dirPath) || !is_dir($dirPath)) {
      throw new \Exception("Invalid path for directory: $dirPath");
    } else if (!is_writable($dirPath)) {
      throw new \Exception("Directory must be writable");
    }
    $removed = [];
    $nowMS = time();
    $oldestMTime = $nowMS - $maxAgeS;
    Log::info("Removing old files from $dirPath");
    $dir = new FilesystemIterator($dirPath);
    $fs = new FileSystem();
    foreach ($dir as $fileInfo) {
      $filePath = $fileInfo->getRealPath();
      if ($fileInfo->isDir() || self::isDotFile($filePath)) {
        continue;
      }
      $mTime = $fileInfo->getMTime();
      if ($mTime <= $oldestMTime) {
        array_push($removed, $filePath);
        $fs->delete($filePath);
      }
    }
    return $removed;
  }
}
