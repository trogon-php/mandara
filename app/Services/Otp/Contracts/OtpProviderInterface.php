<?php

namespace App\Services\Otp\Contracts;

interface OtpProviderInterface
{
    public function sendOtp(string $identifier, string $otp, string $type): bool;
}
