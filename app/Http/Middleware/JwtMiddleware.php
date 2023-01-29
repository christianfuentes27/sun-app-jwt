<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('Authorization');
        $token = explode(' ', $header)[1];
        $key = $_ENV['TOKEN_SECRET'];
        if($token) {
            try {
                $decoded = JWT::decode($token, new Key($key, 'HS256'));
            } catch(\Exception $e) {
                return response()->json(['message' => 'User not valid'], 401);
            }
        }
        return $next($request);
    }
}
