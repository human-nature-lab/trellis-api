<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class RoleAuthMiddleware
{

    public function handle($request, Closure $next, $type, ...$roles)
    {
        $user = $request->user();
        if($user === null){
            return response()->json([
                'err' => "Unauthorized"
            ], Response::HTTP_UNAUTHORIZED);
        }

        $isAuthorized = in_array($user->role, $roles);
        if($type === 'blacklist'){
            $isAuthorized = !$isAuthorized;
        }

        if(!$isAuthorized){
            return response()->json([
                'err' => "Unauthorized"
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);

    }

}
