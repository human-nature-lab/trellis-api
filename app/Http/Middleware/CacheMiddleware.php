<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Log;

class CacheMiddleware {

    public function handle (Request $request, Closure $next, String $cacheControl = null) {

      if (!isset($cacheControl)) {
        $cacheControl = 'max-age=' . 60 * 60 * 24 * 7 . ', public';
      }
      Log::debug("Cache-Control: $cacheControl");

      $response = $next($request);

      if ($request->isMethod('GET')) {
        $response->headers->set('Cache-Control', $cacheControl);
      }

      return $response;

    }
}
