<?php

namespace App\Services;

use App\Models\Snapshot;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SnapshotService {

  public $ignoredTables = [];

  // Tables that don't change really ever and might not have timestamps
  public $staticTables = ['census_type', 'form_type', 'permission'];

  // Tables that only have a created_at date to check
  public $createdAtTables = ['preload_action', 'action'];

   // Tables that we care about, but can use interview.updated_at as a proxy instead
  public $dataTables = ['action', 'datum', 'question_datum', 'survey_condition_tag', 'section_condition_tag', 'edge'];

  public function __construct () {
    app()->configure('snapshot');
    $this->ignoredTables = config('snapshot.ignoredTables');
  }

  public function latestModifiedTimes (bool $quickCheck = true): array {
    $ignored = array_merge($this->ignoredTables, $this->dataTables, $this->staticTables);
    $tables = $this->getSnapshotTables($ignored);
    if (!$quickCheck) {
      $tables = array_merge($tables, $this->dataTables);
    }
    $times = [];
    foreach ($tables as $table) {
      $useCreatedAt = in_array($table, $this->createdAtTables);
      Log::info("Checking $table $useCreatedAt");
      $maxDate = DB::table($table)->
        selectRaw($useCreatedAt ? 'max(created_at) as last_updated' : 'max(updated_at) as last_updated')->
        first();
      $times[$table] = strtotime($maxDate->last_updated);
    }
    return $times;
  }

  public function dbLastModifiedAt (bool $quickCheck = true): int {
    $tableTimes = $this->latestModifiedTimes($quickCheck);
    $latestTime = 0;
    foreach ($tableTimes as $table => $time) {
      if ($time > $latestTime) {
        $latestTime = $time;
      }
    }
    return $latestTime;
  }

  public function snapshotCreatedAt (): int {
    $latestSnapshot = Snapshot::where('deleted_at',null)
      ->select('created_at')
      ->orderBy('created_at', 'desc')
      ->first();
    return strtotime($latestSnapshot['created_at']);
  }

  public function snapshotIsOutdated (bool $quickCheck = true): bool {
    $lastDBUpdateTime = $this->dbLastModifiedAt($quickCheck);
    $latestSnapshotTime = $this->snapshotCreatedAt();
    return $latestSnapshotTime < $lastDBUpdateTime;
  }

  public function getSnapshotTables (array $ignoredTables): array {
    $tables = [];

    // TODO: Laravel 6 adds Schema::allTables() type method that abstracts this
    $tablesQuery = DB::select('SHOW TABLES');
    foreach ($tablesQuery as $raw) {
      $table = array_values(get_object_vars($raw))[0];
      if (!in_array($table, $ignoredTables)) {
        array_push($tables, $table);
      }
    }
    return $tables; 
  }

}