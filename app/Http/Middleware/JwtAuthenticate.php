<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        // Log::info("JwtAuthenticate middleware called");
        try {
            // Only validate the token, don't load the user
            $token = JWTAuth::parseToken();
            
            // Check if token is valid (without loading user from DB)
            if (!$token->check()) {
                return response()->json([
                    'status' => false,
                    'http_code' => 401,
                    'message' => __('messages.unauthenticated'),
                    'data' => (object)[],
                    'errors' => (object)[],
                    'meta' => (object)[],
                ], 401);
            }
        } catch (JWTException $e) {
            Log::error("JwtAuthenticate middleware error: " . $e->getMessage());
            return response()->json([
                'status' => false,
                'http_code' => 401,
                'message' => __('messages.unauthenticated'),
                'data' => (object)[],
                'errors' => (object)[],
                'meta' => (object)[],
            ], 401);
        }
        return $next($request);
    }
}
