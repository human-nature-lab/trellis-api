<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\HookService;
use App\Models\Hook;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class HookController extends Controller {

  public function geoHookStatuses(HookService $hooks, String $geoId) {
    $validator = Validator::make([$geoId], ['required|exists:geo,id']);
    if ($validator->fails()) {
      return response()->json([
        'msg' => $validator->errors(),
      ], Response::HTTP_BAD_REQUEST);
    }
    $hooks = $hooks->getGeoHooks();

    // Join past runs with hooks using the hook_id
    $result = [];
    $instances = Hook::whereIn('hook_id', array_keys($hooks))->where('entity_id', $geoId)->get();
    foreach ($hooks as $hook) {
      $result[$hook->def['id']] = $hook->def;
      $result[$hook->def['id']]['instances'] = [];
    }
    foreach ($instances as $inst) {
      $result[$inst->hook_id]['instances'][] = $inst;
    }
    return response()->json($result);
  }

  public function runGeoHook(HookService $hooks, String $geoId, String $hookId) {
    $validator = Validator::make([$geoId], ['required|exists:geo,id']);
    if ($validator->fails()) {
      return response()->json([
        'msg' => $validator->errors(),
      ], Response::HTTP_BAD_REQUEST);
    }
    $hooks = $hooks->getGeoHooks();
    if (!isset($hooks[$hookId])) {
      return response()->json([
        'msg' => 'invalid hookId',
      ], Response::HTTP_BAD_REQUEST);
    }
    $hook = $hooks[$hookId];
    $instances = Hook::where('hook_id', $hookId)->where('entity_id', $geoId)->get();
    $once = isset($hook->def['once']) && $hook->def['once'];

    // verify the hook can be run again
    if ($once && count($instances) > 0) {
      return response()->json([
        'msg' => 'this hook can only be run once',
      ], Response::HTTP_BAD_REQUEST);
    }

    $instanceId = null;
    if (!$once) {
      $instanceId = Uuid::uuid4();
    }

    set_time_limit(0);

    // Place our record in the DB
    $h = DB::transaction(function () use ($instanceId, $geoId, $hook) {
      $h = new Hook();
      $h->fill([
        'entity_id' => $geoId,
        'instance_id' => $instanceId,
        'hook_id' => $hook->def['id'],
        'started_at' => Carbon::now(),
      ]);
      $h->result = null;
      $h->save();
      return $h;
    });

    try {
      $hook->setup();
      $config = [
        'geoId' => $geoId,
      ];
      $hook->setInput(json_encode($config));
      $res = $hook->run();
      // Complete our record in the DB
      DB::transaction(function () use ($h, $res) {
        if (trim($res) !== '') {
          $h->result = $res;
        }
        $h->finished_at = Carbon::now();
        $h->save();
      });

      return response($res)->header('Content-Type', 'application/json');;
    } catch (\Exception $e) {
      DB::transaction(function () use ($h) {
        $h->delete();
      });
      Log::error($e);
      return response()->json([
        'msg' => $e,
      ], Response::HTTP_BAD_REQUEST);
    }
  }

  public function respondentHookStatuses(HookService $hooks, String $respondentId) {
    $hooks = $hooks->getRespondentHooks();
  }

  public function runRespondentHook(HookService $hooks, String $respondentId, String $hookId) {
    $hooks = $hooks->getRespondentHooks();
  }
}
