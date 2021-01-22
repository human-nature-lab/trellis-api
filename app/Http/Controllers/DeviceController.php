<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Log;
use Validator;
use App\Models\Device;

class DeviceController extends Controller
{
    public function getDevice(Request $request, $id)
    {
        $validator = Validator::make(
            ['id' => $id],
            ['id' => 'required|string|min:36']
        );

        if ($validator->fails() === true) {
            return response()->json([
               'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $deviceModel = Device::find($id);

        if ($deviceModel === null) {
            return response()->json([
                'msg' => 'URL resource not found'
            ], Response::HTTP_OK);
        }

        return response()->json([
            'device' => $deviceModel
        ], Response::HTTP_OK);
    }

    public function getAllDevices(Request $request)
    {
        $devices = Device::with('addedByUser')->get();

        return response()->json(
            ['devices' => $devices],
            Response::HTTP_OK
        );
    }

    public function removeDevice(Request $request, $id)
    {
        $validator = Validator::make(
            ['id' => $id],
            ['id' => 'required|string|min:36']
        );

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $deviceModel = Device::find($id);

        if ($deviceModel === null) {
            return response()->json([
                'msg' => 'URL resource was not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $deviceModel->delete();

        return response()->json([

        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function createDevice (Request $request) {
        Log::info($request->all());
        $validator = Validator::make($request->all(), [
            'device.device_id' =>   'required|string|min:1|max:255',
            'device.name' =>        'required|string|min:1|max:255'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        function RandomToken($length = 32){
            if(!isset($length) || intval($length) <= 8 ){
                $length = 32;
            }
            if (function_exists('random_bytes')) {
                return bin2hex(random_bytes($length));
            }
            if (function_exists('mcrypt_create_iv')) {
                return bin2hex(mcrypt_create_iv($length, MCRYPT_DEV_URANDOM));
            }
            if (function_exists('openssl_random_pseudo_bytes')) {
                return bin2hex(openssl_random_pseudo_bytes($length));
            }
        }

        $userModel = $request->user();

        if (is_null($userModel)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $device = $request->get('device');

        $model = Device::withTrashed()->where('device_id', $device['device_id'])->first();

        // Make a new device if it doesn't exist
        if (!isset($model)) {
            $model = new Device;
            $model->id = Uuid::uuid4();
            $model->device_id = $device['device_id'];
        }

        $deviceKey = RandomToken();
        if (!isset($deviceKey)) {
          return response()->json([
            'message' => 'Unable to create device key. Ensure random_bytes works on this server.'
          ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $model->name = $device['name'];
        $model->key = $deviceKey;
        $model->added_by_user_id = $userModel->id;
        $model->deleted_at = null;
        $model->save();

        return response()->json([
            'device' => $model->makeVisible(['key'])
        ], Response::HTTP_OK);
    }

    public function updateDevice(Request $request, $id)
    {
        $validator = Validator::make(array_merge($request->all(), [
            'id' => $id
        ]), [
            'id' => 'required|string|min:36',
            'device_id' => 'nullable|string|min:1|max:255',
            'name' => 'nullable|string|min:1|max:255'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $deviceModel = Device::find($id);

        if ($deviceModel === null) {
            return response()->json([
                'msg' => 'URL resource not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $deviceModel->fill($request->input());
        $deviceModel->save();

        return response()->json([
            'device' => $deviceModel,
            'msg' => Response::$statusTexts[Response::HTTP_OK]
        ], Response::HTTP_OK);
    }
}
