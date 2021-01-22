<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CacheMiddleware {

    public function handle (Request $request, Closure $next, String $cacheControl = null) {

      if (!isset($cacheControl)) {
        $cacheControl = 'max-age=' . 60 * 60 * 24 * 7 . ', public';
      }

      $response = $next($request);

      if ($request->isMethod('GET')) {
        $response->headers->set('Cache-Control', $cacheControl);
      }

      return $response;

    }
}
