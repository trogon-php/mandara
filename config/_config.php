<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Core Module Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for the Core module.
    | These settings are shared across all modules and can be overridden
    | by individual module configurations.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | File Upload Settings
    |--------------------------------------------------------------------------
    */
    'file_upload' => [
        'default_disk' => env('FILE_UPLOAD_DISK', 'public'),
        'max_file_size' => env('FILE_UPLOAD_MAX_SIZE', 10485760), // 10MB in bytes
        'allowed_types' => [
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'],
            'document' => ['pdf', 'doc', 'docx', 'txt', 'rtf', 'odt', 'pages'],
            'video' => ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm'],
            'audio' => ['mp3', 'wav', 'ogg', 'aac', 'flac', 'm4a'],
            'archive' => ['zip', 'rar', '7z', 'tar', 'gz', 'bz2'],
        ],
        'image_presets' => [
            'thumbnail' => [
                'width' => 150,
                'height' => 150,
                'quality' => 80,
                'crop' => true,
            ],
            'small' => [
                'width' => 300,
                'height' => 300,
                'quality' => 85,
                'crop' => false,
            ],
            'medium' => [
                'width' => 600,
                'height' => 600,
                'quality' => 90,
                'crop' => false,
            ],
            'large' => [
                'width' => 1200,
                'height' => 1200,
                'quality' => 90,
                'crop' => false,
            ],
        ],
        'storage_paths' => [
            'images' => 'uploads/images',
            'documents' => 'uploads/documents',
            'videos' => 'uploads/videos',
            'audio' => 'uploads/audio',
            'archives' => 'uploads/archives',
            'temp' => 'uploads/temp',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit Settings
    |--------------------------------------------------------------------------
    */
    'audit' => [
        'enabled' => env('AUDIT_ENABLED', true),
        'log_events' => [
            'created' => true,
            'updated' => true,
            'deleted' => true,
            'restored' => true,
        ],
        'track_fields' => [
            'created_by',
            'updated_by',
            'deleted_by',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'user_model' => env('AUDIT_USER_MODEL', 'App\Models\User'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Sorting Settings
    |--------------------------------------------------------------------------
    */
    'sorting' => [
        'default_field' => 'created_at',
        'default_direction' => 'desc',
        'allowed_fields' => [
            'id',
            'name',
            'title',
            'created_at',
            'updated_at',
            'sort_order',
            'position',
            'priority',
            'status',
        ],
        'max_sort_fields' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination Settings
    |--------------------------------------------------------------------------
    */
    'pagination' => [
        'default_per_page' => 15,
        'per_page_options' => [10, 15, 25, 50, 100],
        'max_per_page' => 100,
        'show_pagination_info' => true,
        'show_per_page_selector' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'enabled' => env('CORE_CACHE_ENABLED', true),
        'default_ttl' => 3600, // 1 hour in seconds
        'prefix' => 'core_',
        'tags' => [
            'enabled' => true,
            'namespace' => 'core',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Settings
    |--------------------------------------------------------------------------
    */
    'validation' => [
        'custom_messages' => [
            'required' => 'The :attribute field is required.',
            'email' => 'The :attribute must be a valid email address.',
            'unique' => 'The :attribute has already been taken.',
            'exists' => 'The selected :attribute is invalid.',
        ],
        'custom_attributes' => [
            'name' => 'name',
            'email' => 'email address',
            'password' => 'password',
            'title' => 'title',
            'description' => 'description',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | API Settings
    |--------------------------------------------------------------------------
    */
    'api' => [
        'version' => 'v1',
        'rate_limit' => [
            'enabled' => true,
            'max_attempts' => 60,
            'decay_minutes' => 1,
        ],
        'response_format' => [
            'success_key' => 'success',
            'data_key' => 'data',
            'message_key' => 'message',
            'error_key' => 'error',
            'meta_key' => 'meta',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'channels' => [
            'mail' => true,
            'database' => true,
            'broadcast' => false,
            'slack' => false,
        ],
        'default_channel' => 'mail',
        'queue_notifications' => true,
        'retry_attempts' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Settings
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'enabled' => env('CORE_LOGGING_ENABLED', true),
        'channels' => [
            'core' => [
                'driver' => 'daily',
                'path' => storage_path('logs/core.log'),
                'level' => env('LOG_LEVEL', 'debug'),
                'days' => 14,
            ],
        ],
        'log_queries' => env('CORE_LOG_QUERIES', false),
        'log_requests' => env('CORE_LOG_REQUESTS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    */
    'security' => [
        'csrf_protection' => true,
        'xss_protection' => true,
        'content_security_policy' => true,
        'rate_limiting' => true,
        'input_sanitization' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    */
    'performance' => [
        'query_optimization' => true,
        'eager_loading' => true,
        'lazy_loading' => false,
        'database_connection_pooling' => false,
        'response_compression' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Debug Settings
    |--------------------------------------------------------------------------
    */
    'debug' => [
        'enabled' => env('APP_DEBUG', false),
        'show_queries' => env('CORE_DEBUG_QUERIES', false),
        'show_events' => env('CORE_DEBUG_EVENTS', false),
        'show_cache' => env('CORE_DEBUG_CACHE', false),
    ],
];


