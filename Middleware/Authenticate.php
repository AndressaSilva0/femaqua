<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        try {
            // Tenta autenticar o usuário pelo token JWT
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            return response()->json(['error' => 'Token inválido ou não informado.'], 401);
        }

        return $next($request);
    }
}
