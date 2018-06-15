<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Key;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class KeyMiddleware
{
    /**
     * @param  Request  $request
     * @param  Closure  $next
     * @return Response
     */
    public function handle(Request $request, Closure $next)
    {
        $applicationKey = $request->headers->get('X-Key');
        $keyModel = Key::where('hash', $applicationKey)->where('deleted_at', null)->first();

        if ($keyModel === null) {
            return response()->json([
                'msg' => Response::$statusTexts[Response::HTTP_UNAUTHORIZED]
            ], Response::HTTP_UNAUTHORIZED);
        }

        //$request->session()->put('key', $keyModel->id);
        return $next($request);
    }
}
