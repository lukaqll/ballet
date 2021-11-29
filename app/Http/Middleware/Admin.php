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

class Admin extends BaseMiddleware
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

            if( $user->is_admin != 1 )
                throw new Exception('Sem permissão de administrador');

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
