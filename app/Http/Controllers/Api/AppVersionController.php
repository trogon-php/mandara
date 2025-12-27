<?php

namespace App\Http\Controllers\Api;

use App\Services\App\AppVersionService;
use App\Services\Auth\ClientAuthService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AppVersionController extends BaseApiController
{
    public function __construct(
        protected AppVersionService $appVersionService,
        protected ClientAuthService $authService
    ) {}

    /**
     * Return iOS & Android app versions
     * (no authentication required)
     */
    public function index(Request $request)
    {

        $data = $this->appVersionService->getVersions();

        try {
            // Check if token exists and is valid
            $token = JWTAuth::parseToken();
            if ($token->check()) {
                // Get user ID from token
                $userId = $token->getPayload()->get('sub');
                
                // Get registration step if user exists
                $nextStep = $this->authService->getAuthNextStep($userId, false);
                $data['data']['registration_next_step'] = $nextStep;
                $data['data']['onboarding_next_step'] = $this->authService->getOnboardingNextStep($userId);
            }
        } catch (JWTException $e) {
            // No token or invalid token - that's okay, continue without user
        }
        $data['data']['logo_url'] = 'https://mandara-files.trogon.info/app/uploads/media/692ecfc823bf3.png';
        return $this->respondSuccess(
            $data['data'],
            $data['message']
        );
    }
}
