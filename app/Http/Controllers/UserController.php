<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;
use App\Models\User;

class UserController extends Controller
{

	public function getUser(Request $request, $id) {

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

		$userModel = User::find($id);

		if ($userModel === null) {
			return response()->json([
				'msg' => 'URL resource not found'
			], Response::HTTP_OK);
		}

		return response()->json([
			'user' => $userModel
		], Response::HTTP_OK);
	}

	public function getAllUsers(Request $request) {

		if (!empty($request->input('study'))) {
			$userModel = User::join('study', 'user.selected_study_id', '=', 'study.id')
				->get();
		} else {
			$userModel = User::get();
		}

		return response()->json(
			['users' => $userModel],
			Response::HTTP_OK
		);
	}

	public function updateUser(Request $request, $id) {

		$validator = Validator::make(array_merge($request->all(),[
			'id' => $id
		]), [
			'id' => 'required|string|min:36',
			'name' => 'string|min:1|max:255',
			'username' => 'string|min:1|max:63',
			'password' => 'string|min:1|max:63',
			'role' => 'string|min:1|max:64',
			'selected_study_id' => 'string|min:36'
		]);

		if ($validator->fails() === true) {
			return response()->json([
				'msg' => 'Validation failed',
				'err' => $validator->errors()
			], $validator->statusCode());
		}

		$userModel = User::find($id);

		if ($userModel === null) {
			return response()->json([
				'msg' => 'URL resource not found'
			], Response::HTTP_NOT_FOUND);
		}

		$userModel->fill->input();
		$userModel->save();

		return response()->json([
			'msg' => Response::$statusTexts[Response::HTTP_OK]
		], Response::HTTP_OK);
	}

	public function removeUser(Request $request, $id) {

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

		$userModel = User::find($id);

		if ($userModel === null) {
			return response()->json([
				'msg' => 'URL resource was not found'
			], Response::HTTP_NOT_FOUND);
		}

		$userModel->delete();

		return response()->json([

		]);
	}

	public function createUser(Request $request) {

		$validator = Validator::make($request->all(), [
			'name' => 'required|string|min:1|max:255',
			'username' => 'required|string|min:1|max:63',
			'password' => 'required|string|min:1|max:63',
			'role' => 'string|min:1|max:64',
			'selected_study_id' => 'string|min:36'
		]);

		if ($validator->fails() === true) {
			return response()->json([
				'msg' => 'Validation failed',
				'err' => $validator->errors()
			], $validator->statusCode());
		}

		$userId = Uuid::uuid4();
		$userName = $request->input('name');
		$userUsername = $request->input('username');
		$userPassword = bcrypt($request->input('password'));
		$userRole = $request->input('role');
		$userSelectedStudyId = $request->input('selected_study_id');

		$newUserModel = new User;
		$newUserModel->id = $userId;
		$newUserModel->name = $userName;
		$newUserModel->username = $userUsername;
		$newUserModel->password = $userPassword;
		$newUserModel->role = $userRole;
		$newUserModel->selected_study_id = $userSelectedStudyId;
		$newUserModel->save();

		return response()->json([
			'user' => $newUserModel
		], Response::HTTP_OK);
	}
}
