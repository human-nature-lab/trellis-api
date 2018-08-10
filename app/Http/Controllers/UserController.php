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

    public function getAllUsers(Request $request)
    {
        if (!empty($request->input('study'))) {
            $userModel = User::with('studies')->get(['id', 'name', 'username', 'role', 'selected_study_id']);
        } else {
            $userModel = User::get(['id', 'name', 'username', 'role', 'selected_study_id']);
        }

        return response()->json([
            'users' => $userModel,
            ], Response::HTTP_OK);
    }

    public function saveStudy($userId, $studyId)
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

        $user = User::findOrFail($userId);
        $study = Study::findOrFail($studyId);
        $userStudy = new UserStudy;
        $userStudy->id = Uuid::uuid4();
        $userStudy->user_id = $userId;
        $userStudy->study_id = $studyId;
        $userStudy->save();
        //$user->studies()->save($study);
        $userModel = $user::with('studies')->get();
        return response()->json(
            ['user' => $userModel],
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

        if ($userModel === null) {
            return response()->json([
                'msg' => 'URL resource was not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $userModel->delete();

        return response()->json([

        ]);
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
                'studies' => Study::with('locales')->whereNull('deleted_at')->get()
            ], Response::HTTP_OK);
        }
        return response()->json([
            'studies' => $user->studies
        ], Response::HTTP_OK);
    }

    public function createUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:1|max:255',
            'username' => 'required|string|min:1|max:63',
            'password' => 'required|string|min:1|max:63',
            'password_confirmation' => 'required|string|min:1|max:63',
            'role' => 'nullable|string|min:1|max:64',
            'selected_study_id' => 'nullable|string|min:36'
        ]);



        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }

        if ($request->input('password') !==  $request->input('password_confirmation')) {
            return response()->json([
                'msg' => 'Passwords don\'t match',
                'err' => array("Passwords don't match")
            ], Response::HTTP_BAD_REQUEST);
        }


        $userId = Uuid::uuid4();
        $userName = $request->input('name');
        $userUsername = $request->input('username');
        $userPassword = Hash::make($request->input('password'));
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
