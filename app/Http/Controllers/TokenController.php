<?php

namespace App\Http\Controllers;

use App\Models\Token;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class TokenController extends Controller
{
    public function createToken(Request $request)
    {
        if (! $request->has('username') || ! $request->has('pass')) {
            abort(Response::HTTP_BAD_REQUEST);
        }
        $username = $request->input('username');
        $userPass = $request->input('pass');
        $userModel = User::where('username', $username)
            ->first();

        if ($userModel === null) {
            abort(Response::HTTP_FORBIDDEN);
        }
        if (! Hash::check($userPass, $userModel->password)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $tokenModel = new Token;
        $tokenModel->id = Uuid::uuid4();
        $tokenModel->token_hash = Token::createHash();
        $tokenModel->user_id = $userModel->id;
        $tokenModel->key_id  = $request->session()->get('key');
        $tokenModel->save();

        return response()->json([
            'user' => [
                'id' => $userModel->id,
                'name' => $userModel->name
            ],
            'token' => [
                'hash' => $tokenModel->token_hash,
                'exp'  => $_ENV['TOKEN_EXPIRE'],
                'id' => $tokenModel->id
            ]
        ], Response::HTTP_OK);
    }
}
