<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Validator;
use DB;
use App\Models\Device;

class DeviceController extends Controller
{

	public function getDevice(Request $request, $id) {

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

	public function getAllDevices(Request $request) {

        $deviceModel = Device::get();

		return response()->json(
			['devices' => $deviceModel],
			Response::HTTP_OK
		);
	}

	public function removeDevice(Request $request, $id) {

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

	public function createDevice(Request $request) {

		$validator = Validator::make($request->all(), [
			'id' => 'string|min:1|max:255',
			'name' => 'string|min:1|max:255'
		]);

		if ($validator->fails() === true) {
			return response()->json([
				'msg' => 'Validation failed',
				'err' => $validator->errors()
			], $validator->statusCode());
		}

		$id = Uuid::uuid4();
		$deviceName = $request->input('name');
		$deviceId = $request->input('id');

		$newDeviceModel = new Device;
		$newDeviceModel->id = $id;
		$newDeviceModel->name = $deviceName;
		$newDeviceModel->device_id = $deviceId;
		$newDeviceModel->save();

		return response()->json([
			'device' => $newDeviceModel
		], Response::HTTP_OK);
	}

	public function updateDevice(Request $request, $id) {

		$validator = Validator::make(array_merge($request->all(),[
			'id' => $id
		]), [
			'id' => 'required|string|min:36',
			'device_id' => 'string|min:1|max:255',
			'name' => 'string|min:1|max:255'
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

		$deviceModel->fill->input();
		$deviceModel->save();

		return response()->json([
			'msg' => Response::$statusTexts[Response::HTTP_OK]
		], Response::HTTP_OK);
	}
}
