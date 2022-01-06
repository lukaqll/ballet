<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\{
    TokenExpiredException,
    TokenInvalidException
};
class Authenticate extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {

            $user = JWTAuth::parseToken()->authenticate();

            if( $user->status == 'A' )
                throw new Exception('Usuário inativado');

        } catch (Exception $exception) {

            if ($exception instanceof TokenInvalidException){
                $response = ['status' => 'error', 'message' => 'Sem Autorização'];
            }else if ($exception instanceof TokenExpiredException){
                $response = ['status' => 'error', 'message' => 'Autorização Expirada'];
            }else{
                $response = ['status' => 'error', 'message' => $exception->getMessage()];
            }

            return response()->json($response);
        }
        return $next($request);
    }
}
