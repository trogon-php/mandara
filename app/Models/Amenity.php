<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Amenity extends BaseModel
{
    protected $fileFields = [
        'icon' => [
            'folder' => 'amenities',
            'preset' => 'amenities_icon',
            'single' => true,
        ]
    ];
    protected $casts = [
        'status' => 'string',
        'sort_order' => 'integer',
    ];

    /**
     * Get all items for this amenity
     */
    public function items(): HasMany
    {
        return $this->hasMany(AmenityItem::class);
    }

    /**
     * Get active items for this amenity
     */
    public function activeItems(): HasMany
    {
        return $this->hasMany(AmenityItem::class)->where('status', 'active');
    }

    /**
     * Get all bookings for this amenity
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(AmenityBooking::class);
    }

    /**
     * Check if amenity is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Scope for active amenities
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
