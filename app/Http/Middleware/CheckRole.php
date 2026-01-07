<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
    */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = app('authUser') ?? auth('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'http_code' => 401,
                'message' => 'Unauthenticated',
                'data' => (object)[],
                'errors' => (object)[],
                'meta' => (object)[],
            ], 401);
        }

        // Map role names to role IDs
        $allowedRoles = match($role) {
            'delivery_staff' => [Role::ESTORE_DELIVERY_STAFF->value],
            'admin' => [Role::ADMIN->value],
            'doctor' => [Role::DOCTOR->value],
            default => []
        };

        if (!in_array((int)$user->role_id, $allowedRoles)) {
            return response()->json([
                'status' => false,
                'http_code' => 403,
                'message' => 'Access denied. Insufficient permissions.',
                'data' => (object)[],
                'errors' => (object)[],
                'meta' => (object)[],
            ], 403);
        }

        return $next($request);
    }
}
