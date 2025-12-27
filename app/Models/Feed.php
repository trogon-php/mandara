<?php

namespace App\Models;

use App\Models\BaseModel;

class Feed extends BaseModel
{

    protected $casts = [
        'status' => 'integer',
        'sort_order' => 'integer',
        'feed_category_id' => 'integer',
        'course_id' => 'integer',
        'feed_image' => 'array', // JSON field
    ];

    // Define file fields if the model handles file uploads
    protected $fileFields = [
        'feed_image' => [
            'folder' => 'feeds',
            'preset' => 'feeds_image',
            'single' => false, // multiple images (JSON array)
            'array' => true,
        ],
        'feed_video' => [
            'folder' => 'feeds/videos',
            'preset' => 'feeds_video',
            'single' => true, // single video file
        ],
    ];

    // Define relationships
    public function feedCategory()
    {
        return $this->belongsTo(FeedCategory::class, 'feed_category_id');
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('feed_category_id', $categoryId);
    }

}