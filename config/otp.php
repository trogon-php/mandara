<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default OTP Provider
    |--------------------------------------------------------------------------
    | Options: twilio, msg91, aws, email
    */
    'expiry_minutes' => env('OTP_EXPIRY_MINUTES', 10),

    'default' => env('OTP_PROVIDER', '2factor'),

    // 'providers' => [
    //     '2factor' => \App\Services\Otp\Providers\TwoFactorOtpProvider::class,
    //     'twilio' => \App\Services\Otp\Providers\TwilioOtpProvider::class,
    //     'msg91'  => \App\Services\Otp\Providers\Msg91OtpProvider::class,
    //     'aws'    => \App\Services\Otp\Providers\AwsOtpProvider::class
    // ],
    '2factor' => [
        'api_key'     => env('2FACTOR_API_KEY', ''),
        'sender_name'     => env('2FACTOR_SENDER_NAME', 'TRGNMD'),
        'username'        => env('2FACTOR_USERNAME', 'prism'),
        'password'        => env('2FACTOR_PASSWORD', 'stallion123'),
    ],
    'trogon_otp' => [
        'project_id' => env('TROGON_OTP_PROJECT_ID', ''),
        'project_url' => env('TROGON_OTP_PROJECT_URL', ''),
    ]
];
