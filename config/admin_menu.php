<?php

return [

    // Dashboard
    [
        'title' => 'Dashboard',
        'icon'  => 'ri-dashboard-2-line',
        'route' => 'admin/dashboard',
        'can'   => 'dashboard/index',
    ],

    // Student Management
    [
        'title'     => 'Student / Tutor',
        'is_header' => true,
    ],
    [
        'title'    => 'Student Management',
        'icon'     => 'ri-user-line',
        'can'      => 'students/index',
        'children' => [
            ['title' => 'Students',    'route' => 'admin/students',    'can' => 'students/index'],
            ['title' => 'Enrollments', 'route' => 'admin/enrollments', 'can' => 'enrollments/index'],
            ['title' => 'Batches',     'route' => 'admin/batches',     'can' => 'batches/index', 'feature' => 'batches'],
        ],
    ],
    [
        'title' => 'Tutors',
        'icon'  => 'ri-user-star-line',
        'route' => 'admin/tutors',
        'can'   => 'tutors/index',
    ],
    // Cottage Management
    [
        'title'     => 'Cottage Management',
        'is_header' => true,
    ],
    [
        'title' => 'Cottages',
        'icon'  => 'ri-home-line',
        'route' => 'admin/cottages',
        'can'   => 'cottages/index',
    ],
    [
        'title' => 'Cottage Categories',
        'icon'  => 'ri-folder-line',
        'route' => 'admin/cottage-categories',
        'can'   => 'cottage-categories/index',
    ],

    // Course Management
    [
        'title'     => 'Course Management',
        'is_header' => true,
    ],
    [
        'title'    => 'Courses',
        'icon'     => 'ri-book-line',
        'can'      => 'courses/index',
        'children' => [
            ['title' => 'All Courses',       'route' => 'admin/courses',         'can' => 'courses/index'],
            ['title' => 'Course Categories', 'route' => 'admin/categories',      'can' => 'categories/index'],
            ['title' => 'Course Programs',   'route' => 'admin/programs',        'can' => 'programs/index'],
            ['title' => 'Course Tutors',     'route' => 'admin/course-tutors',   'can' => 'course-tutors/index'],
            ['title' => 'Course Reviews',    'route' => 'admin/course-reviews',  'can' => 'course-reviews/index'],
            ['title' => 'Course Features',   'route' => 'admin/course-features', 'can' => 'course-features/index'],
        ],
    ],

    // Live Classes
    [
        'title'     => 'Live Classes',
        'is_header' => true,
    ],
    [
        'title' => 'Live Classes',
        'icon'  => 'ri-live-line',
        'route' => 'admin/live-classes',
        'can'   => 'live-classes/index',
    ],

    // Exam & Questions
    [
        'title'     => 'Exam & Questions',
        'is_header' => true,
    ],
    [
        'title' => 'Question Banks',
        'icon'  => 'ri-folder-line',
        'route' => 'admin/question-banks',
        'can'   => 'question-banks/index',
    ],
    [
        'title' => 'Questions',
        'icon'  => 'ri-question-line',
        'route' => 'admin/questions',
        'can'   => 'questions/index',
    ],
    [
        'title' => 'Exams',
        'icon'  => 'ri-clipboard-line',
        'route' => 'admin/exams',
        'can'   => 'exams/index',
    ],

    // Content & Marketing
    [
        'title'     => 'Content & Marketing',
        'is_header' => true,
    ],
    [
        'title' => 'Banners',
        'icon'  => 'ri-image-line',
        'route' => 'admin/banners',
        'can'   => 'banners/index',
    ],
    [
        'title'    => 'Gallery',
        'icon'     => 'ri-gallery-line',
        'can'      => 'gallery-albums/index',
        'children' => [
            ['title' => 'Gallery Albums', 'route' => 'admin/gallery-albums', 'can' => 'gallery-albums/index'],
            ['title' => 'Gallery Images', 'route' => 'admin/gallery-images', 'can' => 'gallery-images/index'],
        ],
    ],
    [
        'title' => 'Demo Videos',
        'icon'  => 'ri-video-line',
        'route' => 'admin/demo-videos',
        'can'   => 'demo_videos/index',
    ],
    [
        'title' => 'Notifications',
        'icon'  => 'ri-notification-line',
        'route' => 'admin/notifications',
        'can'   => 'notifications/index',
    ],
    [
        'title'    => 'Feeds',
        'icon'     => 'ri-rss-line',
        'can'      => 'feeds/index',
        'children' => [
            ['title' => 'Feeds',           'route' => 'admin/feeds',           'can' => 'feeds/index'],
            ['title' => 'Feed Categories', 'route' => 'admin/feed-categories', 'can' => 'feed-categories/index'],
        ],
    ],
    [
        'title' => 'Blogs',
        'icon'  => 'ri-book-line',
        'route' => 'admin/blogs',
        'can'   => 'blogs/index',
    ],
    [
        'title'    => 'Reels',
        'icon'     => 'ri-play-circle-line',
        'can'      => 'reels/index',
        'children' => [
            ['title' => 'Reels',           'route' => 'admin/reels',           'can' => 'reels/index'],
            ['title' => 'Reel Categories', 'route' => 'admin/reel-categories', 'can' => 'reel-categories/index'],
        ],
    ],
    // [
    //     'title' => 'Reviews',
    //     'icon'  => 'ri-star-line',
    //     'route' => 'admin/reviews',
    //     'can'   => 'reviews/index',
    // ],
    [
        'title' => 'Testimonials',
        'icon'  => 'ri-quote-text',
        'route' => 'admin/testimonials',
        'can'   => 'testimonials/index',
    ],

    // Payments & Packages
    [
        'title'     => 'Payments & Packages',
        'is_header' => true,
    ],
    [
        'title' => 'Cottage Packages',
        'icon'  => 'ri-gift-line',
        'route' => 'admin/cottage-packages',
        'can'   => 'cottage-packages/index',
    ],
    [
        'title' => 'Orders',
        'icon'  => 'ri-shopping-cart-line',
        'route' => 'admin/orders',
    ],
    [
        'title' => 'Payments',
        'icon'  => 'ri-money-dollar-circle-line',
        'route' => 'admin/payments',
    ],

    // Reports
    [
        'title'     => 'Reports',
        'is_header' => true,
    ],
    [
        'title'    => 'Reports',
        'icon'     => 'ri-bar-chart-line',
        'can'      => 'reports/index',
        'children' => [
            ['title' => 'Referral Report',         'route' => 'admin/reports/referrals',              'can' => 'reports/referrals'],
            ['title' => 'Top Referrers',           'route' => 'admin/reports/top-referrers',          'can' => 'reports/top-referrers'],
            // ['title' => 'Lesson File Report',      'route' => 'admin/lesson_files/user_reports',      'can' => 'reports/lesson-files'],
            // ['title' => 'Daily Attendance Report', 'route' => 'admin/live_class/attendance_report',   'can' => 'reports/attendance'],
            // ['title' => 'Consolidated Report',     'route' => 'admin/live_class/consolidated_report', 'can' => 'reports/consolidated'],
        ],
    ],

    // System
    [
        'title'     => 'System',
        'is_header' => true,
    ],
    // Media Library
    [
        'title' => 'Media Library',
        'icon'  => 'ri-folder-image-line',
        'route' => 'admin/media',
        'can'   => 'media/index',
    ],
    [
        'title'    => 'Settings',
        'icon'     => 'ri-settings-3-line',
        'can'      => 'roles/index',
        'children' => [
            ['title' => 'Client Credentials',     'route' => 'admin/client-credentials',     'can' => 'client-credentials/index'],
            ['title' => 'Live Class Integrations', 'route' => 'admin/live-class-integrations', 'can' => 'live-class-integrations/index'],
            ['title' => 'Live Class Accounts',     'route' => 'admin/live-class-accounts',     'can' => 'live-class-accounts/index'],
            ['title' => 'Roles & Permissions',    'route' => 'admin/roles',                   'can' => 'roles/index'],
            ['title' => 'Login Attempts',          'route' => 'admin/login-attempts',         'can' => 'login-attempts/index'],
        ],
    ],
];
