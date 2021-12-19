<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login( Request $request ){

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['status' => 'error', 'message' => 'E-Mail ou Senha incorretos']);
        }

        return $this->respondWithToken($token);
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['status' => 'success', 'data' => null]);
    }

    protected function respondWithToken($token)
    {
        return response()->json([

            'status' => 'success',
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ]
        ]);
    }

    public function getUser(){
        try {
            $result = ['status' => 'success', 'data' => auth('api')->user()];
        } catch ( ValidationException $e ) {
            $result = ['status' => 'error', 'message' => $e->errors()];
        }
        return json_encode($result);
    }

    public function passowrdReset( Request $request ){

        try {

            $data = $request->validate([
                'token' => 'required|string',
                'password' => 'required|string|min:6',
                'password_confirmation' => 'required|string|min:6',
            ]);

            $updated = $this->passwordRecoveryService->resetPassword($data);

            $result = ['status' => 'success', 'data' => $updated];
        } catch ( ValidationException $e ) {
            $result = ['status' => 'error', 'message' => $e->errors()];
        }
        return json_encode($result);
    }

    public function sendMailPasswordReset( Request $request ){

        try {

            $data = $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);

            $user = $this->usersService->get(['email' => $data['email']]);

            if( empty($user) )
                throw ValidationException::withMessages(['usuário não encontrado']);

            $sended = $this->passwordRecoveryService->sendMail($user, false);

            $result = ['status' => 'success', 'data' => $sended];
        } catch ( ValidationException $e ) {
            $result = ['status' => 'error', 'message' => $e->errors()];
        }
        return json_encode($result);
    }
}

