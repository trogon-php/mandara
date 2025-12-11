<?php

namespace App\Services\Auth;
use App\Services\Auth\BaseAuthService;
use App\Enums\Role;

class StudentAuthService extends BaseAuthService
{
    public function login(array $data): array
    {
        return parent::loginWithOtp($data, [Role::STUDENT->value]);
    }

    public function register(array $data): array
    {
        $existing = $this->users->findByPhone($data['phone'], $data['country_code']);
        
        if ($existing) {
            // send OTP for existing user
            return parent::loginWithOtp($data, [Role::STUDENT->value]);
        }

        // Create user with meta data (separated)
        $user = $this->users->storeWithMeta($data);

        // send OTP for new user
        return parent::loginWithOtp($data, [Role::STUDENT->value]);
    }
}