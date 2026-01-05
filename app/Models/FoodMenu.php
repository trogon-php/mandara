<?php

namespace App\Models;

class FoodMenu extends BaseModel
{
    protected $fillable = [
        'food_item_id',
        'menu_date',
        'sort_order',
    ];

    protected $casts = [
        'food_item_id' => 'integer',
        'menu_date' => 'date',
        'sort_order' => 'integer',
    ];

    public function scopeByDate($query, $date)
    {
        return $query->where('menu_date', $date);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('menu_date', [$startDate, $endDate]);
    }

    public function item()
    {
        return $this->belongsTo(FoodItem::class, 'food_item_id');
    }
}