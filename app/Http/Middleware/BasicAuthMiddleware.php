<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Log;

class BasicAuthMiddleware
{

    /**
     * @param  Request  $request
     * @param  Closure  $next
     * @param  String   $type
     * @return Response
     */
    public function handle ($request, Closure $next) {

        $auth = $request->headers->get('Authorization');
        if (isset($auth)) {
            $authParts = explode(' ', $auth);
            if ($authParts[0] === 'Basic') {
                $credsParts = explode(':', base64_decode($authParts[1]));
                if (count($credsParts) === 2) {
                    $username = $credsParts[0];
                    $password = $credsParts[1];
                    $user = User::where('username', $username)->first();

                    $request->setUserResolver(function () use ($user) {
                      return $user;
                    });

                    if (isset($user) && Hash::check($password, $user->password)) {
                        return $next($request);
                    }
                }                
            }
        }

        return response()->json([
            'msg' => 'Invalid user or password'
        ], Response::HTTP_UNAUTHORIZED);

    }

}
