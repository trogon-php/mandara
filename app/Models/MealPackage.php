<?php

namespace App\Models;

use App\Models\BaseModel;

class MealPackage extends BaseModel
{
    protected $fillable = [
        'title',
        'short_description',
        'thumbnail',
        'content',
        'labels',
        'is_veg',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'is_veg' => 'boolean',
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $fileFields = [
        'thumbnail' => [
            'folder' => 'meal_packages',
            'preset' => 'meal_packages_thumbnail',
            'single' => true,
        ],
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    public function scopeVeg($query)
    {
        return $query->where('is_veg', 1);
    }

    public function scopeNonVeg($query)
    {
        return $query->where('is_veg', 0);
    }
}
