<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\Core\CacheService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class IsUserActive
{
    protected string $cachePrefix = 'user';
    protected int $cacheTtl = 300; // 5 minutes

    public function handle(Request $request, Closure $next)
    {
        
        $userId = JWTAuth::parseToken()->getPayload()->get('sub');
        $cacheKey = "{$userId}";
        
        // Get cache service instance
        $cacheService = app(CacheService::class);
        
        // Register the cache key in the registry
        $cacheService->registerKey($this->cachePrefix, $cacheKey);
        
        // Get user from cache or refresh from DB
        $fullCacheKey = "{$this->cachePrefix}:{$cacheKey}";
        $cachedUser = Cache::remember($fullCacheKey, now()->addSeconds($this->cacheTtl), function () use ($userId) {
            Log::info("UserActive middleware cache miss for user: {$userId}");
            return User::find($userId);
        });

        if (!$cachedUser) {
            return response()->json([
                'status'       => false,
                'http_code'  => 401,
                'message'      => __('messages.unauthenticated'),
                'data'         => (object) [],
                'errors'       => (object) [],
                'meta'         => (object) [],
            ], 401);
        }

        // $cacheKey = "user:{$user->id}";

        // Get user from cache or refresh from DB
        // $cachedUser = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($user) {
        //     Log::info("UserActive middleware cache miss for user: {$user->id}");
        //     return $user->fresh();
        // });

        // If blocked, return error
        if (($cachedUser->status ?? 'blocked') == 'blocked' || ($cachedUser->is_blocked ?? false)) {
            return response()->json([
                'status'       => false,
                'http_code'  => 403,
                'message'      => __('messages.account_blocked'),
                'data'         => (object) [],
                'errors'       => (object) [],
                'meta'         => (object) [],
            ], 403);
        }

        // Share with request
        $request->merge(['authUser' => $cachedUser]);

        // Share globally via service container
        app()->instance('authUser', $cachedUser);

        return $next($request);
    }
}
