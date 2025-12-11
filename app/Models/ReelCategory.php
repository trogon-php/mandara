<?php

namespace App\Models;

use App\Models\BaseModel;

class ReelCategory extends BaseModel
{
    protected $casts = [
        'sort_order' => 'integer',
        'thumbnail'  => 'array',
        'status'     => 'boolean',
    ];

    protected $fileFields = [
        'thumbnail' => [
            'folder' => 'reel-categories',
            'preset' => 'reel_categories_thumbnail',
            'single' => true, 
        ],
    ];

    // Relationships
    public function reels()
    {
        return $this->hasMany(Reel::class);
    }

    public function activeReels()
    {
        return $this->hasMany(Reel::class)->active();
    }

    // Scopes
    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    public function scopeCategoriesWithReels($query)
    {
        return $query->whereHas('reels');
    }
}
