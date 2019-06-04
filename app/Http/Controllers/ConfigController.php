<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Services\ConfigService;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;
use Log;

class ConfigController extends Controller {

    public function all (Request $request) {
        $user = $request->user();
        $vals = isset($user) ? Config::all() : Config::where('is_public', 1)->get();
        return response()->json($vals, Response::HTTP_OK);
    }

    public function get ($key) {

        $key = urldecode($key);

        $validator = Validator::make([
            'key' => $key
        ], [
            'key' => 'required|string|max:255|exists:config,key'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors()
            ], $validator->statusCode());
        }

        return response()->json(ConfigService::get($key), Response::HTTP_OK);
    }

    public function set (Request $request) {

        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255|exists:config,key',
            'value' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors()
            ], $validator->statusCode());
        }

        $c = ConfigService::set($request->get('key'), $request->get('value'));
        return response()->json($c, Response::HTTP_ACCEPTED);
    }

    public function reset (Request $request) {

        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255|exists:config,key'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors()
            ], $validator->statusCode());
        }

        return response()->json(ConfigService::reset($request->get('key')), Response::HTTP_ACCEPTED);
    }
}
