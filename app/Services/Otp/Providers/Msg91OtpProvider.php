<?php

namespace App\Services\Otp\Providers;

use App\Services\Otp\Contracts\OtpProviderInterface;
use Illuminate\Support\Facades\Http;

class Msg91OtpProvider implements OtpProviderInterface
{
    public function sendOtp(string $identifier, string $otp, string $type): bool
    {
        // Example Msg91 API
        $response = Http::post('https://api.msg91.com/api/v5/otp', [
            'mobile' => $identifier,
            'otp'    => $otp,
        ]);

        return $response->successful();
    }
}
