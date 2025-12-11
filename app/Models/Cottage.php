<?php

namespace App\Models;

class Cottage extends BaseModel
{
    protected $casts = [
        'cottage_category_id' => 'integer',
        'status' => 'boolean',
        'sort_order' => 'integer',
        'images' => 'array',
    ];
    protected $fileFields = [
        'images' => [
            'folder' => 'cottages',
            'preset' => 'cottages_image',
            'single' => false,
            'array' => true,
        ],
    ];
    public function category()
    {
        return $this->belongsTo(CottageCategory::class, 'cottage_category_id', 'id');
    }
}
