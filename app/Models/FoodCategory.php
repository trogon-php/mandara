<?php

namespace App\Models;

class FoodCategory extends BaseModel
{
    protected $fillable = [
        'title',
        'description',
        'start_time',
        'end_time',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    public function scopeAvailableNow($query)
    {
        $currentTime = now()->format('H:i:s');
        return $query->where('start_time', '<=', $currentTime)
            ->where('end_time', '>=', $currentTime);
    }

    public function items()
    {
        return $this->hasMany(FoodItem::class, 'category_id');
    }
}