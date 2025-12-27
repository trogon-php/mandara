<?php

use App\Models\Role;


return [
    // Category
    'categories' => [
        'index' => [Role::ADMIN],
        'create' => [Role::ADMIN],
        'edit' => [Role::ADMIN],
        'delete' => [Role::ADMIN],
    ],

    // Feeds
    'feeds' => [
        'index' => [Role::ADMIN, Role::NURSE],
        'create' => [Role::ADMIN, Role::NURSE],
        'edit' => [Role::ADMIN, Role::NURSE],
        'delete' => [Role::ADMIN, Role::NURSE],
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
        'index' => [Role::ADMIN, Role::NURSE],
        'create' => [Role::ADMIN, Role::NURSE],
        'edit' => [Role::ADMIN, Role::NURSE],
        'delete' => [Role::ADMIN, Role::NURSE],
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

    // Batch
    'batches' => [
        'index' => [Role::ADMIN],
        'create' => [Role::ADMIN],
        'edit' => [Role::ADMIN],
        'delete' => [Role::ADMIN],
    ],

    // Course
    'courses' => [
        'index' => [Role::ADMIN, Role::NURSE],
        'create' => [Role::ADMIN, Role::NURSE],
        'edit' => [Role::ADMIN, Role::NURSE],
        'delete' => [Role::ADMIN, Role::NURSE],
    ],

    // Course Units
    'course-units' => [
        'index' => [Role::ADMIN, Role::NURSE],
        'create' => [Role::ADMIN, Role::NURSE],
        'edit' => [Role::ADMIN, Role::NURSE],
        'delete' => [Role::ADMIN, Role::NURSE],
    ],

    // Course Tutors
    'course-tutors' => [
        'index' => [Role::ADMIN],
        'create' => [Role::ADMIN],
        'edit' => [Role::ADMIN],
        'delete' => [Role::ADMIN],
    ],

    // Exams
    'exams' => [
        'index' => [Role::ADMIN, Role::NURSE],
        'create' => [Role::ADMIN, Role::NURSE],
        'edit' => [Role::ADMIN, Role::NURSE],
        'delete' => [Role::ADMIN, Role::NURSE],
    ],

    // Course Reviews
    'course-reviews' => [
        'index' => [Role::ADMIN],
        'create' => [Role::ADMIN],
        'edit' => [Role::ADMIN],
        'delete' => [Role::ADMIN],
    ],

    // Course Features
    'course-features' => [
        'index' => [Role::ADMIN, Role::NURSE],
        'create' => [Role::ADMIN, Role::NURSE],
        'edit' => [Role::ADMIN, Role::NURSE],
        'delete' => [Role::ADMIN, Role::NURSE],
    ],

    // Tutors
    'tutors' => [
        'index' => [Role::ADMIN],
        'create' => [Role::ADMIN],
        'edit' => [Role::ADMIN],
        'delete' => [Role::ADMIN],
    ],

    // Students
    'students' => [
        'index' => [Role::ADMIN, Role::NURSE],
        'create' => [Role::ADMIN, Role::NURSE],
        'edit' => [Role::ADMIN, Role::NURSE],
        'delete' => [Role::ADMIN, Role::NURSE],
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

    // Feed
    'feeds' => [
        'index' => [Role::ADMIN, Role::NURSE],
        'create' => [Role::ADMIN, Role::NURSE],
        'edit' => [Role::ADMIN, Role::NURSE],
        'delete' => [Role::ADMIN, Role::NURSE],
    ],

    // Login Attempts
    'login-attempts' => [
        'index' => [Role::ADMIN],
    ]
    
    
];
