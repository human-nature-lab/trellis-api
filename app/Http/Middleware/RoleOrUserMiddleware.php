<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class RoleOrUserMiddleware
{

    /**
     * @param  Request  $request
     * @param  Closure  $next
     * @param  String   $type
     * @return Response
     */
    public function handle(Request $request, Closure $next) {
        $roles = array_slice(func_get_args(), 2);
        $user = $request->user();
        if(!isset($user)){
            return response()->json([
                'err' => "Unauthorized"
            ], Response::HTTP_UNAUTHORIZED);
        }

        $params = $request->route()[2];
        $userId = $params['user_id'];

        if (!isset($userId)) {
            throw new Exception('RoleOrUserMiddleware must be used with the "user_id" route parameter');
        }

        $isAuthorized = $user->id === $userId;

        $isAuthorized = $isAuthorized ? true : in_array($user->role, $roles);

        if(!$isAuthorized){
            return response()->json([
                'err' => "Unauthorized"
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);

    }

}
