<?php

namespace App\Models;


class DietPlan extends BaseModel
{

    protected $fillable = [
        'title',
        'slug',
        'month',
        'image',
        'short_description',
        'content',
        'status',
        'sort_order',
    ];

    protected $fileFields = [
        'image' => [
            'folder' => 'diet_plans',
            'preset' => 'diet_plans_image',
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
}
