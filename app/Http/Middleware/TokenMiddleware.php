<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\Token;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use stdClass;

class TokenMiddleware {
  /**
   * @param  Request  $request
   * @param  Closure  $next
   * @return Response
   */
  public function handle(Request $request, Closure $next) {
    $hash = $request->headers->get('X-Token');
    if ($request->headers->has('Authorization')) {
      $hash = substr($request->headers->get('Authorization'), strlen("bearer "));
    }
    $token = Cache::get($hash);
    $tokenMinutes = (int)env('TOKEN_EXPIRE');
    if (!isset($token)) {
      $tokenModel = Token::where('token_hash', $hash)
        ->where('updated_at', '>=', DB::raw('now() - interval ' . $tokenMinutes . ' minute'))
        ->first();
      if (isset($tokenModel)) {
        $token = ['id' => $tokenModel->id, 'updated_at' => $tokenModel->updated_at];
      }
    }
    $isAuthorized = isset($token) && Carbon::now()->subMinutes($tokenMinutes)->isBefore($token['updated_at']);
    Cache::add($hash, $token, Carbon::now()->add('minutes', $tokenMinutes));
    if (!$isAuthorized) {
      return response()->json([
        'msg' => Response::$statusTexts[Response::HTTP_UNAUTHORIZED]
      ], Response::HTTP_UNAUTHORIZED);
    } else {
      Token::where('id', $token['id'])->update(['updated_at' => Carbon::now()]);
    }

    return $next($request);
  }
}
