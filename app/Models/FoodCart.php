<?php

namespace App\Models;

class FoodCart extends BaseModel
{
    protected $fillable = [
        'user_id',
        'food_item_id',
        'quantity',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'food_item_id' => 'integer',
        'quantity' => 'integer',
    ];

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function item()
    {
        return $this->belongsTo(FoodItem::class, 'food_item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}