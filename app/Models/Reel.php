<?php

namespace App\Models;

use App\Models\BaseModel;

class Reel extends BaseModel
{
    protected $casts = [
        'reel_category_id' => 'integer',
        'sort_order'     => 'integer',
        'thumbnail'      => 'string',
        'video'          => 'string',  // Changed from 'video_url' to 'video'
        'status'         => 'boolean',
        'premium'        => 'boolean',
    ];

    protected $fileFields = [
        'thumbnail' => [
            'folder' => 'reels/thumbnails',
            'preset' => 'reels_thumbnail',
            'single' => true
        ],
        'video' => [
            'folder' => 'reels/videos',
            'preset' => 'reels_video',
            'single' => true
        ],
    ];

    // Relationships
    public function reelCategory()
    {
        return $this->belongsTo(ReelCategory::class);
    }

    // public function course()
    // {
    //     return $this->belongsTo(Course::class);
    // }

    // public function category()
    // {
    //     return $this->belongsTo(Category::class);
    // }

    /**
     * Query scopes
     */
    public function scopePremium($query)
    {
        return $query->where('premium', true);
    }

    public function scopeFree($query)
    {
        return $query->where('premium', false);
    }

    public function scopeByReelCategory($query, $categoryId)
    {
        return $query->where('reel_category_id', $categoryId);
    }

    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
}
