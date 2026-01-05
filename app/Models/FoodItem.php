<?php

namespace App\Models;

class FoodItem extends BaseModel
{
    protected $fillable = [
        'category_id',
        'title',
        'short_description',
        'description',
        'price',
        'image',
        'is_veg',
        'stock',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'category_id' => 'integer',
        'price' => 'decimal:2',
        'stock' => 'integer',
        'status' => 'boolean',
        'is_veg' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $fileFields = [
        'image' => [
            'folder' => 'food/items',
            'preset' => 'food_item_image',
            'single' => true,
            'array' => false,
        ],
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeByFoodType($query, $isVeg)
    {
        return $query->where('is_veg', $isVeg);
    }

    public function category()
    {
        return $this->belongsTo(FoodCategory::class, 'category_id');
    }

    public function menuItems()
    {
        return $this->hasMany(FoodMenu::class, 'food_item_id');
    }

    public function cartItems()
    {
        return $this->hasMany(FoodCart::class, 'food_item_id');
    }

    public function orderItems()
    {
        return $this->hasMany(FoodOrderItem::class, 'food_item_id');
    }
}