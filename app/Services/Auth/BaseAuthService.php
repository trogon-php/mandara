<?php

namespace App\Services\Auth;


use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use App\Enums\Role;
use App\Http\Resources\User\AppUserProfileResource;

use App\Services\Auth\LoginAttemptService;
use App\Services\Users\UserService;

class BaseAuthService
{
    protected UserService $users;
    protected LoginAttemptService $loginAttempts;

    public function __construct(
        UserService $users,
        LoginAttemptService $loginAttempts
    ) {
        $this->users = $users;
        $this->loginAttempts = $loginAttempts;
    }

    public function sendOtp(array $data, array $allowedRoles = []): array
    {
        if($data['type'] == 'phone') {
            $user = $this->users->findByPhone($data['phone'], $data['country_code']);
        } else {
            $user = $this->users->findByEmail($data['email']);
        }
        // dd($user);
        if(! $user) {
            // register user as client
            $data['role_id'] = Role::CLIENT->value;
            $user = $this->users->storeWithMeta($data);

            if($user) {

                return $this->loginWithOtp($data, [Role::CLIENT->value]);
            }
            return ['status' => false, 'message' => __('messages.user_registration_failed'), 'http_code' => Response::HTTP_INTERNAL_SERVER_ERROR];
        }
        return $this->loginWithOtp($data, $allowedRoles);

    }
    /**
     * Common OTP login flow
     */
    public function loginWithOtp(array $data, array $allowedRoles = []): array
    {
        if($data['type'] == 'phone') {
            $user = $this->users->findByPhone($data['phone'], $data['country_code']);
        } else {
            $user = $this->users->findByEmail($data['email']);
        }

        if (! $user) {
            return ['status' => false, 'message' => __('messages.user_not_found'), 'http_code' => Response::HTTP_NOT_FOUND];
        }

        if ($user->status == 'blocked') {
            return ['status' => false, 'message' => __('messages.account_blocked'), 'http_code' => Response::HTTP_FORBIDDEN];
        }

        // Role restriction check
        if (! empty($allowedRoles) && ! in_array($user->role_id, $allowedRoles)) {
            return ['status' => false, 'message' => __('messages.role_not_allowed'), 'http_code' => Response::HTTP_FORBIDDEN];
        }

        // Generate OTP
        $otp = rand(1000, 9999);

        if($data['type'] == 'phone') {

            // Record attempt
            $this->loginAttempts->recordAttempt([
                'user_id'      => $user->id,
                'country_code' => $data['country_code'],
                'phone'        => $data['phone'],
                'channel'      => 'phone',
                'otp_code'     => $otp,
                'ip_address'   => request()->ip(),
                'user_agent'   => request()->userAgent(),
                'status'       => 'pending',
            ]);
    
            // TODO: send OTP
            // $phoneFormatted = $data['country_code'] . $data['phone'];
            $res = send_sms_otp($data['country_code'], $data['phone'], $otp);
            if($res) {
                return ['status' => true, 'message' => __('messages.otp_sent')];
            }
        }
        if($data['type'] == 'email') {
            // Record attempt
            $this->loginAttempts->recordAttempt([
                'user_id'      => $user->id,
                'email'        => $data['email'],
                'channel'      => 'email',
                'otp_code'     => $otp,
                'ip_address'   => request()->ip(),
                'user_agent'   => request()->userAgent(),
                'status'       => 'pending',
            ]);

            // TODO: send OTP
            $res = send_email_otp($data['email'], $otp);
            if($res) {
                return ['status' => true, 'message' => __('messages.otp_sent')];
            }
        }
        return [
            'status' => false,
            'message' => 'Something went wrong while sending OTP',
            'http_code' => Response::HTTP_INTERNAL_SERVER_ERROR
        ];
    }

    /**
     * Verify OTP and issue JWT token
     */
    public function verifyOtp(array $data, bool $forApi = true): array
    {
        if($data['type'] == 'phone') {
            $attempt = $this->loginAttempts->findByPhone($data['phone'], $data['country_code']);
            $user = $this->users->findByPhone($data['phone'], $data['country_code']);
        } else {
            $attempt = $this->loginAttempts->findByEmail($data['email']);
            $user = $this->users->findByEmail($data['email']);
        }

        if (! $attempt || $attempt->otp_code !== $data['otp']) {
            return ['status' => false, 'message' => __('messages.otp_invalid'), 'http_code' => Response::HTTP_UNAUTHORIZED];
        }

        $this->loginAttempts->markAsVerified($attempt->id);

        if (! $user) {
            return ['status' => false, 'message' => __('messages.user_not_found'), 'http_code' => Response::HTTP_UNAUTHORIZED];
        }

        if ($forApi) {
            // Create minimal JWT token with id and role_id
            $token = JWTAuth::customClaims([
                'sub' => $user->id, // User ID
                'role_id' => $user->role_id, // Role ID
                'iat' => time(),
                'exp' => time() + (60 * 60 * 24 * 1000), // 1 hour
            ])->fromUser($user);

            $user = (new AppUserProfileResource($user))->toArray(request());
            
            return [
                'status' => true,
                'data' => [
                    'token' => $token, 
                    'user' => $user
                    ]
            ];
        }

        Auth::login($user);
        return ['status' => true, 'user' => $user];
    }

    /**
     * Refresh JWT token
     */
    public function refreshToken(): array
    {
        try {
            $token = JWTAuth::refresh();
            return ['status' => true, 'token' => $token];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => __('messages.token_refresh_error'), 'http_code' => Response::HTTP_UNAUTHORIZED];
        }
    }

    /**
     * Logout and invalidate JWT token
     */
    public function logout(bool $fromApi = true): array
    {
        try {
            if ($fromApi) {
                JWTAuth::invalidate();
            } else {
                Auth::logout();
            }
            return ['status' => true, 'message' => __('messages.logout_success')];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => __('messages.logout_failed'), 'http_code' => Response::HTTP_UNAUTHORIZED];
        }
    }
}