<?php

namespace App\Http\Controllers\Api;

use App\Enums\Role;
use App\Http\Controllers\Api\BaseApiController;
use App\Services\Auth\AuthService;
use App\Services\Auth\ClientAuthService;
use App\Services\Users\UserMetaService;
use Illuminate\Http\Request;

class AuthController extends BaseApiController
{
    public function __construct(
        protected AuthService $authService,
        protected ClientAuthService $clientAuthService,
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

        $result = $this->clientAuthService->register($user->id, $data);

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
        if($result['status'] == true && $result['data']['user']['role_id'] == Role::CLIENT->value) {
            // checking any registeration step is pending
            $nextStep = $this->clientAuthService->getAuthNextStep($result['data']['user']['id']);
            $result['data']['next_step'] = $nextStep;
            $data['data']['onboarding_next_step'] = $this->clientAuthService->getOnboardingNextStep($result['data']['user']['id']);
        }
        return $this->serviceResponse($result, __('messages.login_success'));
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
        $result = $this->clientAuthService->updateDob($user->id, $data);
        return $this->serviceResponse($result);
    }

    // Update Pregnancy
    public function updateJourney(Request $request)
    {
        $user = $this->getAuthUser();
        if(! $user) {
            return $this->respondUnauthorized();
        }
        $data = $request->validate([
            'preparing_to_conceive' => 'nullable|integer|in:0,1',
            'last_period_date' => 'required_if:preparing_to_conceive,1|date',
            'is_pregnant' => 'nullable|integer|in:0,1',
            'delivery_date' => 'required_if:is_pregnant,1|date',
            'is_delivered' => 'nullable|integer|in:0,1',
            'baby_dob' => 'required_if:is_delivered,1|date',
        ]);
        // dd($data);

        $result = $this->clientAuthService->updateJourney($user->id, $data);

        return $this->serviceResponse($result);
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
