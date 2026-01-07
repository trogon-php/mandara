<?php

return [

    // Dashboard
    [
        'title' => 'Dashboard',
        'icon'  => 'ri-dashboard-2-line',
        'route' => 'admin/dashboard',
        'can'   => 'dashboard/index',
    ],

    // Users Management
    [
        'title'     => 'User Management',
        'is_header' => true,
    ],
    [
        'title'    => 'User Management',
        'icon'     => 'ri-user-line',
        'can'      => 'clients/index',
        'children' => [
            ['title' => 'Clients',    'route' => 'admin/clients',    'can' => 'clients/index'],
            ['title' => 'Guests', 'route' => 'admin/guests', 'can' => 'guests/index'],
            ['title' => 'Doctors', 'route' => 'admin/doctors', 'can' => 'doctors/index'],
            ['title' => 'Nurses', 'route' => 'admin/nurses', 'can' => 'nurses/index'],
            ['title' => 'Kitchen Staff', 'route' => 'admin/kitchen-staff', 'can' => 'kitchen-staff/index'],
            ['title' => 'Attendants', 'route' => 'admin/attendants', 'can' => 'attendants/index'],
            ['title' => 'Estore Delivery Staff', 'route' => 'admin/estore-delivery-staff', 'can' => 'estore-delivery-staff/index'],
            ['title' => 'Food Delivery Staff', 'route' => 'admin/food-delivery-staff', 'can' => 'food-delivery-staff/index'],
            ['title' => 'Front Office', 'route' => 'admin/front-office', 'can' => 'front-office/index'],
        ],
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


    // Estore Management
    [
        'title'     => 'Estore Management',
        'is_header' => true,
    ],
    [
        'title' => 'Estore Products',
        'icon'  => 'ri-shopping-bag-line',
        'route' => 'admin/estore-products',
        'can'   => 'estore-products.index',
    ],
    [
        'title' => 'Estore Categories',
        'icon'  => 'ri-folder-line',
        'route' => 'admin/estore-categories',
        'can'   => 'estore-categories.index',
    ],
    [
        'title' => 'Estore Orders',
        'icon'  => 'ri-shopping-cart-line',
        'route' => 'admin/estore-orders',
        'can'   => 'estore-orders/index',
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
    [
        'title'     => 'PUBLIC TOOLS',
        'is_header' => true,
    ],
    [
        'title' => 'Diet Plans',
        'icon'  => 'ri-restaurant-line',
        'route' => 'admin/diet-plans',
        'can'   => 'diet-plans/index',
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
        'title' => 'Meal Packages',
        'icon'  => 'ri-restaurant-line',
        'route' => 'admin/meal-packages',
        'can'   => 'meal-packages/index',
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

    // FrontOffice Management
    [
        'title'     => 'FrontOffice Management',
        'is_header' => true,
    ],
    [
        'title' => 'Mandara Bookings',
        'icon'  => 'ri-calendar-line',
        'route' => 'admin/mandara-bookings',
        'can'   => 'mandara-bookings/index',
    ],
    [
        'title' => 'Mandara Payments',
        'icon'  => 'ri-money-dollar-circle-line',
        'route' => 'admin/mandara-payments',
        'can'   => 'mandara-payments/index',
    ],
    [
        'title' => 'Mandara Booking Questions',
        'icon'  => 'ri-question-line',
        'route' => 'admin/mandara-booking-questions',
        'can'   => 'mandara-booking-questions/index',
    ],
    // Amenities
    [
        'title'     => 'Amenities Management',
        'is_header' => true,
    ],
    [
        'title' => 'Amenities',
        'icon'  => 'ri-building-line',
        'route' => 'admin/amenities',
        'can'   => 'amenities/index',
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
