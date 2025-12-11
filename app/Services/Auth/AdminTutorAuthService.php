<?php

namespace App\Services\Auth;

use App\Enums\Role;

class AdminTutorAuthService extends BaseAuthService
{
    public function login(array $data): array
    {
        return parent::loginWithOtp($data, [Role::ADMIN->value, Role::TUTOR->value]);
    }
}
