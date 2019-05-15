<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Services\ConfigService;

class DemoMiddleware
{

    /**
     * @param  Request  $request
     * @param  Closure  $next
     * @return Response
     */
    public function handle (Request $request, Closure $next) {
        if (ConfigService::get('serverMode') === 'demo') {
            return $next($request);
        } else {
            return abort(Response::HTTP_NOT_FOUND);
        }
    }
}
