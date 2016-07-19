<?php

namespace App\Services;

use App\Models\User;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Auth;
use Session;
use App\Http\Requests\Request;

class UserService
{
    public static function getActiveUsersPaginated($perPage)
    {
        $users = User::whereNotIn('username', ['admin'])
            ->orderBy('created_at')
            ->paginate($perPage);

        return $users;
    }

    public static function getCurrentUserId()
    {
        $userId = Auth::User()->id;

        return $userId;
    }

    public static function createNewUser(Request $request)
    {
        $user = new User;
        $user->id = Uuid::uuid4();
        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->password = bcrypt($request->input('password'));

        $user->save();

        return $user;
    }

    public static function updateUser(Request $request, $id)
    {
        $user = User::find($id);
        $updated = false;

        if ($request->input('name') != $user->name) {
            $user->name = $request->input('name');
            $updated = true;
        }
        if ($request->input('username') != $user->username) {
            $user->username = $request->input('username');
            $updated = true;
        }
        if ($request->input('password') != '') {
            $user->password = bcrypt($request->input('password'));
            $updated = true;
        }
        if ($updated) {
            $user->save();
            $request->session()->flash('message', 'User updated successfully!');
            $request->session()->flash('alert-class', 'success');
        }

        return $user;
    }

    public static function deleteUser($id)
    {
        $user = User::destroy($id);

        return $user;
    }

    public static function updateUserSelectedStudy($userId, $studyId)
    {
        User::where('id', $userId)
            ->update(['selected_study_id' => $studyId]);
        session(['selected_study_id' => $studyId]);
    }

    public static function getUserSelectedStudyId($userId)
    {
        if (session('selected_study_id') == null) {
            $selectedStudyId = User::select('selected_study_id')
                ->where('id', $userId)
                ->get();
            if ($selectedStudyId == null) {
                return redirect('/studies')->with('message', 'No Active Study set. Please select a study.')->with('status', 'error')->with('alert-class', 'danger');
            }
            session(['selected_study_id', $selectedStudyId]);

            return $selectedStudyId;
        } else {

            return session('selected_study_id');
        }
    }
}