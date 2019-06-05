<?php

namespace App\Http\Middleware;

use App\Models\Device;
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
    public function handle ($request, Closure $next) {
        $deviceId =  $request->route()[2]['device_id'];
        $deviceKey = $request->headers->get('X-Key');

        $validator = Validator::make([
            'deviceId' => $deviceId,
            'deviceKey' => $deviceKey
        ], [
            'deviceId' => 'required|string',
            'deviceKey' => 'required|string|min:10|max:255'
        ]);

        $deviceModel = Device::where('device_id', $deviceId)
            ->where('key', $deviceKey)
            ->whereNull('deleted_at')
            ->first();

        if ($validator->fails() || !isset($deviceModel)) {
            return response()->json([
                'msg' => 'Unable to authenticate this device'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);

    }

}
