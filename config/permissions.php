<?php

use App\Models\Role;


return [
    // Dashboard - all admin panel roles can access
    'dashboard' => [
        'index' => [Role::ADMIN, Role::DOCTOR, Role::NURSE, Role::ATTENDANT, Role::ESTORE_DELIVERY_STAFF],
    ],
    // Category
    'categories' => [
        'index' => [Role::ADMIN],
        'create' => [Role::ADMIN],
        'edit' => [Role::ADMIN],
        'delete' => [Role::ADMIN],
    ],

    // Feeds
    'feeds' => [
        'index' => [Role::ADMIN],
        'create' => [Role::ADMIN],
        'edit' => [Role::ADMIN],
        'delete' => [Role::ADMIN],
    ],

    // Feed Categories
    'feed-categories' => [
        'index' => [Role::ADMIN],
        'create' => [Role::ADMIN],
        'edit' => [Role::ADMIN],
        'delete' => [Role::ADMIN],
    ],

    // Reels
    'reels' => [
        'index' => [Role::ADMIN,],
        'create' => [Role::ADMIN,],
        'edit' => [Role::ADMIN],
        'delete' => [Role::ADMIN,],
    ],

    // Reel Categories
    'reel-categories' => [
        'index' => [Role::ADMIN],
        'create' => [Role::ADMIN],
        'edit' => [Role::ADMIN],
        'delete' => [Role::ADMIN],
    ],

    // Reports
    'reports' => [
        'index' => [Role::ADMIN],
        'referrals' => [Role::ADMIN],
        'top-referrers' => [Role::ADMIN],
        'lesson-files' => [Role::ADMIN],
        'attendance' => [Role::ADMIN],
        'consolidated' => [Role::ADMIN],
    ],

    // Packages
    'packages' => [
        'index' => [Role::ADMIN],
        'create' => [Role::ADMIN],
        'edit' => [Role::ADMIN],
        'delete' => [Role::ADMIN],
    ],

    // Package Items
    'package-items' => [
        'index' => [Role::ADMIN],
        'create' => [Role::ADMIN],
        'edit' => [Role::ADMIN],
        'delete' => [Role::ADMIN],
    ],

    // Login Attempts
    'login-attempts' => [
        'index' => [Role::ADMIN],
    ],

    'mandara-bookings' => [
        'index' => [Role::ADMIN],
        'create' => [Role::ADMIN],
        'edit' => [Role::ADMIN],
        'delete' => [Role::ADMIN],
    ],
    'estore-products' => [
        'index' => [Role::ADMIN],
        'create' => [Role::ADMIN],
        'edit' => [Role::ADMIN],
        'delete' => [Role::ADMIN],
    ],
    'estore-categories' => [
        'index' => [Role::ADMIN],
        'create' => [Role::ADMIN],
        'edit' => [Role::ADMIN],
        'delete' => [Role::ADMIN],
    ],
    
];
