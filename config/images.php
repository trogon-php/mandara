<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default image quality
    |--------------------------------------------------------------------------
    | Compression quality for JPG/WEBP images (1-100).
    */
    'quality' => 85,

    /*
    |--------------------------------------------------------------------------
    | Presets
    |--------------------------------------------------------------------------
    | Define target resolutions for different use cases.
    | 
    | - Single-size: [width, height]
    | - Multi-size: ['name' => [width, height], ...]
    */
    'presets' => [

        // Profile pictures (single size)
        'profile_picture' => [250, 250],


        // Feed images (single size, square)
        'feed'    => [1080, 1080],

        // Gallery images (single size)
        'gallery_image' => [1080, 1080],
        'gallery_thumbnail' => [512, 512],

        // Courses (multi-size)
        'courses_thumbnail' => [
            'original' => [800, 600],
            'medium' => [400, 300],
            'thumb'    => [200, 150],
        ],

        'course_units_thumbnail' => [
            'original' => [450, 350],
            'thumb'    => [110, 80],
        ],
        'course_materials_thumbnail' => [300, 200],

        'courses_banner' => [1280, 720],

        'banners_image' => [375, 272],
        'blogs_image' => [800, 600],

        'cottage_categories_thumbnail' => [1080, 1080],
        'cottages_image' => [800, 600],
    
        'reels_thumbnail' => [1080, 1920],
        'reels_video' => [1920, 1080],

        'categories_thumbnail' => [
            'original' => [1000, 800],
            'thumb'    => [250, 200],
        ],

        'programs_thumbnail' => [
            'original' => [1000, 800],
            'thumb'    => [250, 200],
        ],

        'homework' => [
            'original' => [1200, 800],
            'thumb'    => [300, 300],
            'homepage' => [800, 600],
        ],
        
        // reviews
        'reviews_profile_image' => [300, 300],
        // notifications
        'notifications_image' => [1000, 750],

        'testimonials_profile_image' => [300, 300],

        'feeds_image' => [800, 600],
        'feeds_video' => [1920, 1080],
        
        'roles_profile_image' => [300, 300],
        
        // Demo videos (multi-size)
        'demo_videos_thumbnail' => [
            'original' => [900, 600],
            'thumb'    => [300, 200],
        ],

        'question_images' => [800, 600],
        'paragraph_images' => [800, 600],
        'option_image' => [800, 600],
        'assignments_files' => [800, 600],
        'homeworks_files' => [800, 600],
        
    'fallbacks' => [
        'profile_picture' => 'images/default-avatar.png',
        'reviews_profile_image' => 'images/default-avatar.png',
        'notifications_image' => 'images/default-avatar.png',
        'testimonials_profile_image' => 'images/default-avatar.png',
        'feeds_image' => 'images/feed-placeholder.png',
        'feeds_video' => 'images/video-placeholder.png',
        'course_thumbnail'  => 'images/course-placeholder.png',
        'banner'  => 'images/default-banner.jpg',
        'roles_profile_image' => 'images/default-avatar.png',
    ],

    ],

];
