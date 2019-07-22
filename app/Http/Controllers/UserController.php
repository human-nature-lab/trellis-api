<?php

namespace App\Http\Controllers;

use App\Services\PermissionService;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;
use Ramsey\Uuid\Uuid;
use App\Models\User;
use App\Models\UserStudy;
use App\Models\Study;

class UserController extends Controller
{
    public function getUser(Request $request, $id)
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

        $userModel = User::with('role')->find($id);

        if ($userModel === null) {
            return response()->json([
                'msg' => 'URL resource not found'
            ], Response::HTTP_OK);
        }

        return response()->json([
            'user' => $userModel
        ], Response::HTTP_OK);
    }

    public function getUsersPage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'nullable|integer|min:0',
            'size' => 'nullable|integer|min:5|max:100',
            'sortBy' => 'nullable|string|in:name,username,role',
            'descending' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors()
            ], $validator->statusCode());
        }

        $page = $request->get('page') ?: 0;
        $size = $request->get('size') ?: 20;
        $sortBy = $request->get('sortBy') ?: 'name';
        $descending = $request->get('descending') ?: false;

        $q = User::with('role')
            ->orderBy($sortBy, $descending ? 'desc' : 'asc');
        if (!empty($request->input('study'))) {
            $q = $q->with('role', 'studies');
        }

        $totalUsers = $q->count();
        $skip = $page * $size;
        $users = $q->skip($skip)->take($size)->get();

        return response()->json([
            'total' => $totalUsers,
            'start' => $skip,
            'count' => $size,
            'users' => $users,
            ], Response::HTTP_OK);
    }

    public function addStudy($userId, $studyId)
    {
        $validator = Validator::make([
            'user_id' => $userId,
            'study_id' => $studyId], [
            'user_id' => 'required|string|min:36|exists:user,id',
            'study_id' => 'required|string|min:36|exists:study,id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $userStudy = new UserStudy;
        $userStudy->id = Uuid::uuid4();
        $userStudy->user_id = $userId;
        $userStudy->study_id = $studyId;
        $userStudy->save();

        return response()->json(
            ['user_study' => $userStudy],
            Response::HTTP_OK
        );
    }

    public function deleteStudy($userId, $studyId)
    {
        $validator = Validator::make([
            'user_id' => $userId,
            'study_id' => $studyId], [
            'user_id' => 'required|string|min:36',
            'study_id' => 'required|string|min:36'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $userStudy = UserStudy::where('user_id', $userId)
            ->where('study_id', $studyId)
            ->firstOrFail();

        $userStudy->delete();

        return response()->json(
            [],
            Response::HTTP_OK
        );
    }

    public function updatePassword (PermissionService $permissionService, Request $request, $userId) {

        $validator = Validator::make(array_merge($request->all()), [
            'userId' => $userId
        ], [
            'userId' => 'string|min:36|exists:user,id',
            'oldPassword' => 'nullable|string',
            'newPassword' => 'string|min:8'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $requestUser = $request->user();
        $user = User::find($userId);
        if (!$permissionService->hasPermission($requestUser, 'CHANGE_PASSWORDS') && !Hash::check($request->get('oldPassword'), $user->password)) {
            return response()->json([
                'msg' => "Old password doesn't match"
            ], Response::HTTP_BAD_REQUEST);
        }

        $newHash = Hash::make($request->get('newPassword'));
        $user->password = $newHash;
        $user->save();

        return response()->json([
            'msg' => 'Successfully updated password'
        ], Response::HTTP_OK);
    }

    public function updateUser (Request $request, $id) {
        $validator = Validator::make(array_merge($request->all(), [
            'id' => $id
        ]), [
            'id' => 'required|string|min:36|exists:user,id',
            'name' => 'nullable|string|min:1|max:255',
            'username' => 'nullable|string|min:1|max:63',
            'password' => 'nullable|string|min:1|max:63',
            'role_id' => 'nullable|string|min:1|exists:role,id',
            'selected_study_id' => 'nullable|string|min:36'
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

        $all = $request->all();
        unset($all['password']); // Make sure we don't overwrite the password with gibberish
        $userModel->fill($all);
        if ($request->has('password') && !is_null($request->get('password'))) {
            $userPassword = Hash::make($request->get('password'));
            $userModel->password = $userPassword;
        }
        $userModel->save();

        return response()->json([
            'user' => User::with('role', 'studies')->find($id)
        ], Response::HTTP_OK);
    }

    public function removeUser(Request $request, $id)
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

        $userModel = User::find($id);

        if ($userModel === null || $userModel->username === 'admin') {
            return response()->json([
                'msg' => 'URL resource was not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $userModel->delete();

        return response()->json([]);
    }

    public function getMe(Request $request){
        return response()->json($request->user(), Response::HTTP_OK);
    }

    /**
     * Return all of the studies for the signed in user
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getMyStudies (Request $request) {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'msg' => "Can't determine user automagically"
            ], Response::HTTP_BAD_REQUEST);
        } else if ($user->role === 'ADMIN') {
            return response()->json([
                'studies' => Study::whereNull('deleted_at')->get()
            ], Response::HTTP_OK);
        }
        return response()->json([
            'studies' => $user->studies
        ], Response::HTTP_OK);
    }

    public function createUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|min:1|max:255',
            'username' => 'string|min:1|max:63',
            'password' => 'nullable|string|min:8',
            'role_id' => 'string|min:1|exists:role,id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $user = new User;
        $user->fill($request->all());
        $user->id = Uuid::uuid4();
        if ($request->has('password')) {
            $user->password = Hash::make($request->get('password'));
        }
        $user->save();

        return response()->json([
            'user' => User::with('role', 'studies')->find($user->id)
        ], Response::HTTP_OK);
    }
}
