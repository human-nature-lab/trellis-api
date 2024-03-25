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
        array_push($vals, array_key_exists($header, $row) ? $row[$header] : '');
      }
      fputcsv($handle, $vals);
    }
    rewind($handle);
    $csv = stream_get_contents($handle);
    fclose($handle);
    return $csv;
  }

  // Use fgetcsv to parse a csv string and return an array of rows. The first row is assumed to be headers if $fields is
  // not provided.
  static public function stringToAssociativeArrays ($csv, $fields = []) {
    $handle = fopen('php://temp', 'r+');
    fwrite($handle, $csv);
    rewind($handle);
    if (empty($fields)) {
      $fields = fgetcsv($handle);
    }
    $rows = [];
    while (($row = fgetcsv($handle)) !== false) {
      $assoc = [];
      foreach ($fields as $i => $field) {
        $assoc[$field] = $row[$i];
      }
      array_push($rows, $assoc);
    }
    fclose($handle);
    return $rows;
  }

}
