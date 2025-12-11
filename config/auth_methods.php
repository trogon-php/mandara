<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enabled Authentication Methods
    |--------------------------------------------------------------------------
    | Each app can pick its allowed methods.
    */
    'enabled' => [
        'email_password',
        'username_password',
        'email_otp',
        'phone_otp',
        'google',
        'apple',
    ],

     // map method keys → strategy classes
    'map' => [
        'email_password'    => \App\Services\Auth\Methods\EmailPasswordLogin::class,
        'username_password' => \App\Services\Auth\Methods\UsernamePasswordLogin::class,
        'email_otp'         => \App\Services\Auth\Methods\EmailOtpLogin::class,
        'phone_otp'         => \App\Services\Auth\Methods\PhoneOtpLogin::class,
        'google'            => \App\Services\Auth\Methods\GoogleLogin::class,
        'apple'             => \App\Services\Auth\Methods\AppleLogin::class,
    ],
];
