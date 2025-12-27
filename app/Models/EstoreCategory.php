<?php

namespace App\Models;


class EstoreCategory extends BaseModel
{

    protected $fillable = [
        'title',
        'description',
        'sort_order',
        'status',
    ];

    protected $casts = [
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

    public function products()
    {
        return $this->hasMany(EstoreProduct::class, 'category_id');
    }
}
