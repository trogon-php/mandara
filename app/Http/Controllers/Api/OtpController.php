<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Auth\RequestPhoneOtpRequest;
use App\Http\Requests\Api\Auth\VerifyPhoneOtpRequest;
use App\Models\Otp;
use App\Services\Core\BaseApiService;

class OtpController extends BaseApiController
{
    // You can extract to OtpService if you prefer.
    protected BaseApiService $api;

    public function __construct()
    {
        $this->api = new class extends BaseApiService {};
    }

    public function requestPhoneOtp(RequestPhoneOtpRequest $request)
    {
        $otp = rand(100000, 999999);

        Otp::create([
            'identifier'   => $request->phone,
            'country_code' => $request->country_code,
            'otp_code'     => $otp,
            'type'         => 'phone',
            'provider'     => $request->provider ?? 'default',
            'expires_at'   => now()->addMinutes(5),
        ]);

        // Send via provider… (Twilio/MSG91/SNS)
        // OtpProvider::send($request->country_code.$request->phone, $otp);

        return response()->json(
            $this->api->success([], 'OTP sent successfully'),
            200
        );
    }

    public function verifyPhoneOtp(VerifyPhoneOtpRequest $request)
    {
        // We purposely DO NOT issue token here to keep
        // “verify-only” endpoint pure. The strategy does the login.
        $otp = Otp::where('identifier', $request->phone)
            ->where('country_code', $request->country_code)
            ->where('otp_code', $request->otp)
            ->where('type', 'phone')
            ->whereNull('verified_at')
            ->where('expires_at', '>=', now())
            ->latest()
            ->first();

        if (!$otp) {
            return $this->respondError('Invalid or expired OTP', 401);
        }

        $otp->update(['verified_at' => now()]);
        return $this->respondSuccess([], 'OTP verified');
    }
}
