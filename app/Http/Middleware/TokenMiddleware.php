<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\Token;

class TokenMiddleware
{
    /**
     * @param  Request  $request
     * @param  Closure  $next
     * @return Response
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->headers->get('X-Token');

        $tokenModel = Token::where('token_hash', $token)
            ->where('created_at', '>=', DB::raw('now() - interval '.$_ENV['TOKEN_EXPIRE'].' minute'))
            ->first();

        if ($tokenModel === null) {
            return response()->json([
                'msg' => Response::$statusTexts[Response::HTTP_UNAUTHORIZED]
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
