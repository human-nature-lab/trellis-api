<?php namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserConfirmation;
use App\Services\ConfigService;
use App\Services\DemoService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Mail\EmailConfirmation;
use Illuminate\Support\Facades\Mail;
use Log;
use Ramsey\Uuid\Uuid;
use Throwable;
use Validator;

class DemoController extends Controller {

  public function confirmEmail (DemoService $demoService, Request $request, $key) {

    $key = urldecode($key);

    // TODO: Confirm everything and run the setup for this user
    $validator = Validator::make([
      'key' => $key
    ], [
      'key' => 'required|string|min:50|max:255'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'msg' => 'Unable to confirm this email.'
      ], $validator->statusCode());
    }

    $confirmation = UserConfirmation::find($key);

    if (!isset($confirmation)) {
      return response()->json([
        'msg' => 'This confirmation is invalid.'
      ], Response::HTTP_I_AM_A_TEAPOT);
    }

    $existingUser = User::where('username', $confirmation->username)->first();
    if (isset($existingUser)) {
      return response()->json([
        'msg' => 'This username has already been confirmed by another user.'
      ], Response::HTTP_I_AM_A_TEAPOT);
    }

    if (isset($confirmation->deleted_at) || $confirmation->created_at->lessThan(Carbon::now()->subMinutes(ConfigService::get('demo.expirationTime')))) {
      return response()->json([
        'msg' => 'This confirmation code has expired. Please register again.'
      ], Response::HTTP_I_AM_A_TEAPOT);
    }

    try {
      DB::beginTransaction();
      $confirmation->is_confirmed = true;
      $confirmation->deleted_at = Carbon::now();
      $confirmation->save();
      $demoService->makeDemoUser($confirmation, 'supervisor');
      DB::commit();
    } catch (Throwable $e) {
      DB::rollBack();
      throw $e;
    }

    return response()->json([
      'msg' => 'Successfully confirmed the email ' . $confirmation->email . '.'
    ], Response::HTTP_CREATED);

  }

  /**
   * Create a UserConfirmation object in the database. The user will not be created until the email is confirmed.
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
   */
  public function createUserConfirmation (Request $request) {

    $validator = Validator::make($request->all(), [
      'email' => 'required|string|email',
      'username' => 'required|string|alpha_num|unique:user,username',
      'password' => 'required|string|min:5'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'msg' => $validator->errors()
      ], $validator->statusCode());
    }

    // Check if the email has already been confirmed
    $completedEmailConfirmation = UserConfirmation::where('is_confirmed', 1)->where('email', $request->get('email'))->first();
    if (isset($completedEmailConfirmation)) {
      return response()->json([
        'msg' => 'Cannot register with this email'
      ], Response::HTTP_BAD_REQUEST);
    }

    // Check if the username has already been assigned
    $completedUsernameConfirmation = UserConfirmation::where('is_confirmed', 1)->where('username', $request->get('username'))->first();
    if (isset($completedUsernameConfirmation)) {
      return response()->json([
        'msg' => 'This username has already been taken'
      ], Response::HTTP_BAD_REQUEST);
    }

    $confirmation = new UserConfirmation;
    $confirmation->key = Hash::make($request->get('email'));
    $confirmation->email = $request->get('email');
    $confirmation->name = $request->get('name');
    $confirmation->password = Hash::make($request->get('password'));
    $confirmation->is_confirmed = false;
    $confirmation->username = $request->get('username');
    $confirmation->save();

    Log::info($confirmation->key);

    // Actually send the email out for confirmation
    Mail::to($request->get('email'))->send(new EmailConfirmation($confirmation->key, $request->get('name')));

    return response()->json([
      'msg' => 'User created. Email sent to ' . $confirmation->email . '.'
    ], Response::HTTP_CREATED);

  }

  /**
   * Generates a previous for the email confirmation
   */
  public function previewConfirmation ($name) {
    $name = urldecode($name);
    return (new EmailConfirmation(Hash::make($name), $name))->render();
  }

}