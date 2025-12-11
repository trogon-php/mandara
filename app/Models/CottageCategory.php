<?php

namespace App\Models;

class CottageCategory extends BaseModel
{
    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];
    protected $fileFields = [
        'thumbnail' => [
            'folder' => 'cottage_categories',
            'preset' => 'cottage_categories_thumbnail',
            'single' => false,
            'json' => true,
        ],
    ];
    public function cottages()
    {
        return $this->hasMany(Cottage::class);
    }
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
}
