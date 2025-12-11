<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseApiController extends Controller
{
    protected $user;

    public function __construct()
    {
        // Don't set user in constructor - let middleware handle it
        $this->user = null;
    }

    /**
     * Standard success response
     */
    protected function respondSuccess($data = [], string $message = 'Success', int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'status'       => true,
            'http_code'  => $status,
            'message'      => $message,
            'data'         => $data,
            'errors'       => (object) [],
            'meta'         => (object) [],
        ], $status);
    }

    /**
     * Standard error response
     */
    protected function respondError(string $message = 'Error', int $status = Response::HTTP_BAD_REQUEST, $errors = []): JsonResponse
    {
        return response()->json([
            'status'       => false,
            'http_code'  => $status,
            'message'      => $message,
            'data'         => (object) [],
            'errors'       => $errors,
            'meta'         => (object) [],
        ], $status);
    }

    /**
     * Validation error response (422)
     */
    protected function respondValidationError(string $message = 'Validation failed', $errors = []): JsonResponse
    {
        return $this->respondError($message, Response::HTTP_UNPROCESSABLE_ENTITY, $errors);
    }

    /**
     * Unauthorized response (401)
     */
    protected function respondUnauthorized(string $message = 'Unauthenticated. Please login again.'): JsonResponse
    {
        return $this->respondError($message, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Forbidden response (403)
     */
    protected function respondForbidden(string $message = 'Your account is not allowed to perform this action.'): JsonResponse
    {
        return $this->respondError($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Service layer response passthrough
     */
    protected function serviceResponse(array $result, string $successMessage = 'Success'): JsonResponse
    {
        $httpCode = $result['status']
            ? Response::HTTP_OK
            : ($result['http_code'] ?? Response::HTTP_BAD_REQUEST);

        return response()->json([
            'status'       => $result['status'],
            'http_code'  => $httpCode,
            'message'      => $result['message'] ?? $successMessage,
            'data'         => $result['data'] ?? (object) [],
            'errors'       => $result['errors'] ?? (object) [],
            'meta'         => $result['meta'] ?? (object) [],
        ], $httpCode);
    }

    /**
     * Paginated response
     */
    protected function respondPaginated($paginator, string $message = 'Fetched successfully'): JsonResponse
    {
        return response()->json([
            'status'       => true,
            'http_code'  => Response::HTTP_OK,
            'message'      => $message,
            'data'         => $paginator->items(),
            'errors'       => (object) [],
            'meta'         => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'from'         => $paginator->firstItem(),
                'to'           => $paginator->lastItem(),
            ]
        ], Response::HTTP_OK);
    }

    /**
     * Get authenticated user from middleware or auth guard
     */
    protected function getAuthUser()
    {
        // First try to get from middleware (set by IsUserActive middleware)
        // Note: app('authUser') is set by IsUserActive middleware
        $user = app('authUser');
        
        if (!$user) {
            // Fallback to auth guard
            $user = auth('api')->user();
        }
        
        return $user;
    }

    /**
     * Get authenticated user or return 401 response
     */
    protected function getUserOrFail()
    {
        $user = $this->getAuthUser();

        if (!$user) {
            return $this->respondUnauthorized();
        }

        // Optional: check if user is blocked/inactive
        if (isset($user->status) && $user->status === 'blocked') {
            return $this->respondForbidden(__('messages.account_blocked'));
        }

        return $user;
    }
}
