<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login( Request $request ){

        try {

            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string'
            ]);
    
            if (! $token = auth('api')->attempt($credentials)) {
                throw ValidationException::withMessages(['E-Mail ou Senha incorretos']);
            }
    
            $user = auth('api')->user();
            
            if( $user->status == 'MP' )
                throw ValidationException::withMessages(['Seu cadastro está em análise', 'Em breve entraremos em contato']);

            if( $user->status == 'I' )
                throw ValidationException::withMessages(['Cadastro inativado']);

            return $this->respondWithToken($token); 
        } catch ( ValidationException $e ){

            return response()->json(['status' => 'error', 'message' => $e->errors() ]); 
        }

        
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['status' => 'success', 'data' => null]);
    }

    protected function respondWithToken($token)
    {

        $user = auth('api')->user();
        $redirectTo = '/';

        if( $user->is_admin == 1 )
            $redirectTo = '/admin';

        return response()->json([

            'status' => 'success',
            'data' => [
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => auth()->factory()->getTTL() * 60,
                'redirect_to'  => $redirectTo
            ]
        ]);
    }

    public function getUser(){
        try {
            $result = ['status' => 'success', 'data' => new UserResource(auth('api')->user())];
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

