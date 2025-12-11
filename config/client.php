<?php

return [
    'name' => 'Client',
    'description' => 'Client',
    'contact' => [
        'email' => 'info@trogon.info',
        'phone' => '+919946801100',
        'whatsapp' => '+919946801100',
    ],

    // Features
    'features' => [
        'programs' => false,
        'categories' => true,
        'batches' => true,
        'can_purchase' => [
            'categories' => true,
            'courses' => true,
            'course_units' => true,
            'course_materials' => true,
        ],
        'video_options' => [
            'vimeo' => 'Vimeo',
            'youtube' => 'YouTube',
            // 's3' => 'S3 Storage',
            // 'local' => 'Local Storage',
        ],
        'material_options' => [
            'video' => 'Video',
            'document' => 'Document',
            // 'audio' => 'Audio',
            // 'text' => 'Text',
            // 'scorm' => 'SCORM',
            // 'live_class' => 'Live Class',
            // 'quiz' => 'Quiz',
            // 'exam' => 'Exam',
            // 'assignment' => 'Assignment',
            // 'other' => 'Other',
        ],
        'courses' => [
            'content' => [
                'nested_units' => false,
                'section_materials' => false,
            ],
        ],
        'exams' => [
            'endpoints' => [
                'available' => true,
                'upcoming' => true,
                'attempted' => true,
                'unattempted' => true,
                'available_unattempted' => true,
                'available_attempted' => true,
            ],
        ],
    ],

    'app' => [
        'home' => [
            'course_type' => 'category_courses',
            'banners' => true,
            'courses' => true,
            'feeds' => true,
            'reviews' => true,
            'courses' => true,
        ],
        'my_course' => [
            'shortcuts' => [
                'live_classes' => true,
                'practice' => true,
                'exam' => true
            ],
            'exam_url' => 'https://laravel.trogon.info/exam/',
            'promotion' => [
                'image_url' => 'https://lv-files.trogon.info/medyuva/uploads/media/promotion.jpeg',
                'action_url' => 'https://laravel.trogon.info/practice/',
            ]
        ],
        
    ],

    // App Home Page
    'app_home' => [
        'shared' => [
            'banners' => true,
            'courses' => true,
        ],
        'user_specific' => [
        ],
    ]
];