<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\Auth\ClientAuthService;
use App\Services\Auth\StudentAuthService;
use App\Services\Users\UserMetaService;
use Illuminate\Http\Request;

class AuthController extends BaseApiController
{
    public function __construct(
        protected ClientAuthService $authService,
        protected UserMetaService $userMetaService
    )
    {}

    public function register(Request $request)
    {
        $user = $this->getAuthUser();

        if($user->email == null) {
            $baseRules = [
                'name'         => 'required|string|max:255',
                'email'        => 'required|string|email|max:255|unique:users,email',
            ];
        }
        if($user->phone == null) {
            $baseRules = [
                'name'         => 'required|string|max:255',
                'country_code' => 'required|string|max:5',
                'phone'        => 'required|string|max:20', 
            ];
        }
        if($user->email != null && $user->phone != null) {
            return $this->respondSuccess(null, 'User already registered');
        }

        // Get user meta validation rules from config (only enabled fields)
        $metaRules = $this->userMetaService->getValidationRules();
        
        // Merge base and meta rules
        $validationRules = array_merge($baseRules, $metaRules);

        $data = $request->validate($validationRules);

        $result = $this->authService->register($user->id, $data);

        return $this->serviceResponse($result);
    }

    // Send OTP
    public function sendOtp(Request $request)
    {
        $data = $request->validate([
            'country_code' => 'required_if:type,phone|string|max:5',
            'phone'        => 'required_if:type,phone|string|max:20',
            'email'        => 'required_if:type,email|string|email|max:255',
            'type'         => 'required|string|in:phone,email',
        ]);

        $result = $this->authService->sendOtp($data);
        return $this->serviceResponse($result, __('messages.otp_sent'));
    }
    // Update DOB
    public function updateDob(Request $request)
    {
        $user = $this->getAuthUser();
        if(! $user) {
            return $this->respondUnauthorized();
        }

        $data = $request->validate([
            'date_of_birth' => 'required|date',
        ]);
        $result = $this->authService->updateDob($user->id, $data);
        return $this->serviceResponse($result);
    }
    // Update Pregnancy
    public function updatePregnancy(Request $request)
    {
        $user = $this->getAuthUser();
        if(! $user) {
            return $this->respondUnauthorized();
        }
        $data = $request->validate([
            'is_pregnant' => 'required|integer|in:0,1',
            'delivery_date' => 'required_if:is_pregnant,1|date',
            'have_siblings' => 'required|integer|in:0,1',
        ]);

        $result = $this->authService->updatePregnancy($user->id, $data);

        return $this->serviceResponse($result);
    }

    /**
     * Student Login (OTP request)
     */
    // public function login(Request $request)
    // {
    //     $data = $request->validate([
    //         'country_code' => 'required|string|max:5',
    //         'phone'        => 'required|string|max:20',
    //     ]);

    //     $result = $this->authService->login($data);
    //     return $this->serviceResponse($result, __('messages.otp_sent'));
    // }

    /**
     * Verify OTP (API → issue JWT token)
     */
    public function verifyOtp(Request $request)
    {
        $data = $request->validate([
            'country_code' => 'required_if:type,phone|string|max:5',
            'phone'        => 'required_if:type,phone|string|max:20',
            'email'        => 'required_if:type,email|string|email|max:255',
            'otp'          => 'required|string|max:6',
            'type'         => 'required|string|in:phone,email',
        ]);

        $result = $this->authService->verifyOtp($data, true);
        return $this->serviceResponse($result, __('messages.login_success'));
    }

    /**
     * Refresh JWT token
     */
    public function refresh()
    {
        $result = $this->authService->refreshToken();
        return $this->serviceResponse($result, __('messages.token_refreshed'));
    }

    /**
     * Logout (JWT token invalidation)
     */
    public function logout()
    {
        $result = $this->authService->logout(true);
        return $this->serviceResponse($result, __('messages.logout_success'));
    }

    /**
     * Get user meta options
     */
    public function getUserMetaOptions()
    {
        $options = $this->userMetaService->getFieldOptions();
        
        return $this->serviceResponse([
            'status' => true,
            'data' => $options
        ], 'User meta options retrieved successfully');
    }
}
