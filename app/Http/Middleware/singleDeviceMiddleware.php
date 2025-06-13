<?php

namespace App\Http\Middleware;

use App\Class\ResponseClass;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class singleDeviceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('api')->user();
        $currentToken = JWTAuth::getToken();

        if ($currentToken != $user->token) {
            return ResponseClass::error('Unauthenticated.', statusCode: 401);
        }

        return $next($request);
    }
}
