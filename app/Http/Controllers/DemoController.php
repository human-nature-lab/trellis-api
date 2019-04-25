<?php namespace App\Http\Controllers;

use App\Models\UserConfirmation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;
use Validator;

class DemoController extends Controller {

	public function confirmEmail (Request $request, $key) {
	    // TODO: Confirm everything and run the setup for this user
        $validator = Validator::make([
            'key' => $key,
            'email' => $request->get('email')
        ], [
            'key' => 'required|string|min:200',
            'email' => 'required|email|exists:user_confirmation,email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'Unable to confirm this email'
            ]);
        }

        $confirmation = UserConfirmation::find($key);

        if (!isset($confirmation) || !Hash::check($confirmation->email, $confirmation->key)) {
            return response()->json([
                'msg' => 'This confirmation is invalid'
            ], Response::HTTP_I_AM_A_TEAPOT);
        }

        if (isset($confirmation->deleted_at) || $confirmation->created_at->lessThan(Carbon::now()->subDays(2))) {
            return response()->json([
                'msg' => 'This confirmation has expired'
            ], Response::HTTP_I_AM_A_TEAPOT);
        }

        $confirmation->is_confirmed = true;
        $confirmation->deleted_at = Carbon::now();
        $confirmation->save();

        return response()->json([
            'msg' => 'Successfully confirmed the email ' . $confirmation->email
        ], Response::HTTP_OK);

    }

    public function createUser (Request $request) {

	    $validator = Validator::make($request->all(), [
	        'email' => 'required|string|email|unique:user_confirmation,email',
            'username' => 'required|string|alpha_num|unique:user,username',
            'password' => 'required|string|min:5'
        ]);

	    if ($validator->fails()) {
	        return response()->json([
	            'msg' => $validator->errors()
            ], $validator->statusCode());
        }

	    $confirmation = new UserConfirmation;
	    $confirmation->key = Hash::make($request->get('email'));
	    $confirmation->email = $request->get('email');
	    $confirmation->is_confirmed = false;
	    $confirmation->username = $request->get('username');

    }

}