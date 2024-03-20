<?php

namespace app\Services;

class CsvService {

  // Use fputcsv to write rows as a string
  static public function rowsToString ($headers, $rows) {
    $handle = fopen('php://temp', 'r+');
    fputcsv($handle, $headers);
    foreach ($rows as $row) {
      $vals = [];
      foreach ($headers as $header) {
        array_push($vals, $row[$header]);
      }
      fputcsv($handle, $vals);
    }
    rewind($handle);
    $csv = stream_get_contents($handle);
    fclose($handle);
    return $csv;
  }

}
