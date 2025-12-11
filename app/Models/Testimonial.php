<?php

namespace App\Models;

use App\Models\BaseModel;

class Testimonial extends BaseModel
{
    protected $casts = [
        'status' => 'integer',
        'rating' => 'integer',
        'sort_order' => 'integer',
    ];

    // Define file fields if the model handles file uploads
    protected $fileFields = [
        'profile_image' => [
            'folder' => 'testimonials', // folder in uploads
            'type' => 'testimonials_profile_image', // preset name from config/images.php
            'single' => true, // single image only
        ],
    ];

    // Define relationships if needed
    // public function category()
    // {
    //     return $this->belongsTo(Category::class);
    // }
}