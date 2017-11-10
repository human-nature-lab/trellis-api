<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class RoleAuthMiddleware
{

    public function handle($request, Closure $next, ...$roleWhitelist)
    {
        $token = $request->headers->get('X-Token');

        $tokenModel = Token::where('token_hash', $token)
            ->where('created_at', '>=', DB::raw('now() - interval '.$_ENV['TOKEN_EXPIRE'].' minute'))
            ->first();

        $user = $tokenModel->user();

        if(!in_array($user->role, $roleWhitelist)){
            return response()->json([
                'err' => "This method is unavailable",
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
