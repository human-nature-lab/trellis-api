<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class RoleAuthMiddleware
{

    /**
     * @param  Request  $request
     * @param  Closure  $next
     * @param  String   $type
     * @return Response
     */
    public function handle(Request $request, Closure $next, $type)
    {
        $roles = array_slice(func_get_args(), 3);
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
