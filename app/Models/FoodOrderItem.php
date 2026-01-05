<?php

namespace App\Models;

class FoodOrderItem extends BaseModel
{
    protected $fillable = [
        'order_id',
        'food_item_id',
        'item_title',
        'item_description',
        'is_veg',
        'unit_price',
        'quantity',
        'total_amount',
    ];

    protected $casts = [
        'order_id' => 'integer',
        'food_item_id' => 'integer',
        'is_veg' => 'boolean',
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
        'total_amount' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(FoodOrder::class, 'order_id');
    }

    public function item()
    {
        return $this->belongsTo(FoodItem::class, 'food_item_id');
    }
}