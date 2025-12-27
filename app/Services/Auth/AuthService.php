<?php

namespace App\Services\Auth;

use App\Enums\Role;

class AuthService extends BaseAuthService
{
    public function sendOtp(array $data, array $allowedRoles = []): array
    {
        if(empty($allowedRoles)) {
            $allowedRoles = [
                Role::CLIENT->value,
                Role::DOCTOR->value,
                Role::NURSE->value,
                Role::ATTENDANT->value,
                Role::ESTORE_DELIVERY_STAFF->value
            ];
        }
        return parent::sendOtp($data, $allowedRoles);
    }

    public function verifyOtp(array $data, bool $forApi = true): array
    {
        return parent::verifyOtp($data, $forApi);
    }
}
