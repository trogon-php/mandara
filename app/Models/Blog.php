<?php

namespace App\Models;


class Blog extends BaseModel
{
    protected $table = 'blogs';

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $fileFields = [
        'image' => [
            'folder' => 'blogs',
            'preset' => 'blogs_image',
            'single' => true,
        ],
    ];

    /**
     * Scope for active blogs
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Generate slug from title if not provided
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($blog) {
            if (empty($blog->slug) && !empty($blog->title)) {
                $blog->slug = \Illuminate\Support\Str::slug($blog->title);
            }
        });

        static::updating(function ($blog) {
            if ($blog->isDirty('title') && empty($blog->slug)) {
                $blog->slug = \Illuminate\Support\Str::slug($blog->title);
            }
        });
    }
}
