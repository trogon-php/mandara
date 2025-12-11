<?php

namespace App\Services\Otp\Providers;

use App\Services\Otp\Contracts\OtpProviderInterface;
use Illuminate\Support\Facades\Http;

class TwilioOtpProvider implements OtpProviderInterface
{
    public function sendOtp(string $identifier, string $otp, string $type): bool
    {
        // Replace with actual Twilio API call
        $response = Http::post('https://api.twilio.com/send', [
            'to'   => $identifier,
            'body' => "Your OTP is: $otp",
        ]);

        return $response->successful();
    }
}
