<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeviceMiddleware
{

    /**
     * @param  Request  $request
     * @param  Closure  $next
     * @param  String   $type
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        $deviceId =  $request->route()[2]['device_id'];
        $validator = Validator::make([
            'deviceId' => $deviceId
        ], [
            'deviceId' => 'required|string|exists:device,device_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors()
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);

    }

}
