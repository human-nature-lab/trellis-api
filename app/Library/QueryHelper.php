<?php

namespace App\Library;

use Illuminate\Database\Query\Builder;

class QueryHelper {

  static function quickChunk (int $size, Builder $q, String $column, \Closure $f, String $lastIdKey = '__sort_key') {
    $lastId = null;
    $c = 0;
    do {
      $query = clone $q;
      $query->addSelect("$column as $lastIdKey");
      $query = $query->orderBy($column)->limit($size);
      if ($lastId) {
        $query->where($column, '>', $lastId);
      }
      $results = $query->get();
      $resultCount = count($results);
      if ($resultCount > 0) {
        $f($results);
        $lastId = $results[0]->$lastIdKey;
      }
      if ($c > 3) {
        abort(567);
      }
      $c++;
    } while ($resultCount >= $size);

  }

  /**
   * Helper function to create MySQL prepared statements
   * @param String $query
   * @param array $arguments
   * @return String
   */
  static function preparedSql (String $query, array $arguments): String {
    $statement = "SET @query = '$query';" .
                 "PREPARE stmt1 FROM @query;";

    $alphabet = 'abcdefghijklmnopqrstuvwxyz';
    $varNames = [];
    foreach ($arguments as $i => $arg) {
      $varName = "@$i";
      array_push($varNames, $varName);
      $statement .= "SET $varName = ";
      switch (gettype($arg)) {
        case "string":
          $statement .= "'$arg'";
          break;
        default:
          $statement .= $arg;
      }
      $statement .= ";";
    }

    $statement .= "EXECUTE stmt1 USING " . join(', ', $varNames) . ';';
    $statement .= "DEALLOCATE PREPARE stmt1;";
    return $statement;
  }

}