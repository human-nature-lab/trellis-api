<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use App\Models\Token;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserMiddleware
{

    public function handle($request, Closure $next)
    {
        $request->setUserResolver(function () use ($request) {
            $token = $request->headers->get('X-Token');
            if($token === null){
                return null;
            }
            $tokenModel = Token::where('token_hash', $token)
                ->where('created_at', '>=', DB::raw('now() - interval '.$_ENV['TOKEN_EXPIRE'].' minute'))
                ->first();
            if($tokenModel === null){
                return null;
            }
            $user = User::where('id', '=', $tokenModel->user_id)->first();
            return $user;
        });

        return $next($request);
    }
}
