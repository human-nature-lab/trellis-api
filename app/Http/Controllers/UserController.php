<?php

namespace App\Http\Controllers;

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

        $userModel = User::find($id)->get(['id', 'name', 'username', 'role', 'selected_study_id']);

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

        $q = User::select(['id', 'name', 'username', 'role', 'selected_study_id'])
            ->orderBy($sortBy, $descending ? 'desc' : 'asc');
        if (!empty($request->input('study'))) {
            $q = $q->with('studies');
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

    public function updatePassword (Request $request, $userId) {

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
        if ($requestUser->role !== 'ADMIN' && !Hash::check($request->get('oldPassword'), $user->password)) {
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

    public function updateUser(Request $request, $id)
    {
        $validator = Validator::make(array_merge($request->all(), [
            'id' => $id
        ]), [
            'id' => 'required|string|min:36|exists:user,id',
            'name' => 'nullable|string|min:1|max:255',
            'username' => 'nullable|string|min:1|max:63',
            'password' => 'nullable|string|min:1|max:63',
            'role' => 'nullable|string|min:1|max:64',
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

        $userPassword = Hash::make($request->input('password'));
        $userModel->fill($request->input());
        $userModel->password = $userPassword;
        $userModel->save();

        return response()->json([
            'msg' => Response::$statusTexts[Response::HTTP_OK]
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
            'role' => 'string|min:1|max:64'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        $user = new User;
        $user->id = Uuid::uuid4();
        $user->name = $request->get('name');
        $user->username = $request->get('username');
        if ($request->has('password')) {
            $user->password = Hash::make($request->get('password'));
        }
        $user->role = $request->get('role');
        $user->save();

        return response()->json([
            'user' => $user
        ], Response::HTTP_OK);
    }
}
