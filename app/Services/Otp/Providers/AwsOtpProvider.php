<?php

namespace App\Services\Otp\Providers;

use App\Services\Otp\Contracts\OtpProviderInterface;

class AwsOtpProvider implements OtpProviderInterface
{
    public function sendOtp(string $identifier, string $otp, string $type): bool
    {
        // Example AWS SNS
        // SNS SDK integration here
        return true;
    }
}
