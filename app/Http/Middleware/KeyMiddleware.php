<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Key;
use Illuminate\Http\Response;

class KeyMiddleware
{
    public function handle($request, Closure $next)
    {
        $applicationKey = $request->headers->get('X-Key');
        $keyModel = Key::where('hash', $applicationKey)->where('deleted_at', null)->first();

        if ($keyModel === null) {
            return response()->json([
                'msg' => Response::$statusTexts[Response::HTTP_UNAUTHORIZED]
            ], Response::HTTP_UNAUTHORIZED);
        }

        $request->session()->put('key', $keyModel->id);
        return $next($request);
    }
}
