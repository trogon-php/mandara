<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mobile App Versions
    |--------------------------------------------------------------------------
    |
    | These values are used by the AppVersion API to let the mobile
    | applications know the minimum and latest supported versions.
    | You can update them when you release new versions.
    |
    */

    'ios' => [
        'latest'   => '0.0.2',   // latest available on App Store
        'minimum'  => '0.0.1',   // minimum version supported
        'force_update' => true,  // whether to force update
        'review_version' => '0.0.2', // version to review
        'app_url' => 'https://apps.apple.com/us/app/trogon/id6759456162',
    ],

    'android' => [
        'latest'   => '0.0.2',   // latest available on Play Store
        'minimum'  => '0.0.1',   // minimum version supported
        'force_update' => false, // optional
        'review_version' => '0.0.2', // version to review
        'app_url' => 'https://play.google.com/store/apps/details?id=com.trogon.app',
    ],

];
