<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\Token;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserMiddleware
{

    /**
     * @param  Request  $request
     * @param  Closure  $next
     * @return Response
     */
    public function handle(Request $request, Closure $next)
    {
        $request->setUserResolver(function () use ($request) {
            $token = $request->headers->get('X-Token');
            if($token === null){
                return null;
            }
            $tokenModel = Token::where('token_hash', $token)->first();
            if($tokenModel === null){
                return null;
            }
            $user = User::with('role')->find($tokenModel->user_id);
            return $user;
        });

        return $next($request);
    }
}
