<?php

namespace App\Services\App;

use App\Services\Traits\CacheableService;

abstract class AppBaseService
{
    use CacheableService;

    /**
     * Get the authenticated user from request cache
     */
    protected function getAuthUser()
    {
        return app('authUser'); // Set by IsUserActive middleware
    }

    /**
     * Create a structured response payload
     */
    protected function payload(array $data, string $message = 'Success'): array
    {
        return [
            'data' => $data,
            'message' => $message
        ];
    }
}
