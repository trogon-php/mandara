<?php

namespace App\Models;

class EstoreProduct extends BaseModel
{

    protected $fillable = [
        'category_id',
        'title',
        'short_description',
        'description',
        'price',
        'mrp',
        'stock',
        'status',
        'is_featured',
        'images',
    ];

    protected $casts = [
        'category_id' => 'integer',
        'price' => 'decimal:2',
        'mrp' => 'decimal:2',
        'stock' => 'integer',
        'status' => 'boolean',
        'is_featured' => 'boolean',
        'images' => 'array',
    ];
    protected $fileFields = [
        'images' => [
            'folder' => 'estore/products',
            'preset' => 'estore_product_image',
            'single' => false,
            'array' => true,
        ],
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', 1);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function category()
    {
        return $this->belongsTo(EstoreCategory::class, 'category_id');
    }

    public function cartItems()
    {
        return $this->hasMany(EstoreCart::class, 'product_id');
    }

    public function orderItems()
    {
        return $this->hasMany(EstoreOrderItem::class, 'product_id');
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->mrp && $this->mrp > $this->price) {
            return round((($this->mrp - $this->price) / $this->mrp) * 100, 2);
        }
        return 0;
    }
}
