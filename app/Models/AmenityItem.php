<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AmenityItem extends BaseModel
{
    protected $casts = [
        'amenity_id' => 'integer',
        'duration_minutes' => 'integer',
        'price' => 'decimal:2',
        'status' => 'string',
        'sort_order' => 'integer',
    ];

    /**
     * Get the amenity that owns this item
     */
    public function amenity(): BelongsTo
    {
        return $this->belongsTo(Amenity::class);
    }

    /**
     * Get all packages that include this item
     */
    public function packages()
    {
        return $this->belongsToMany(
            CottagePackage::class,
            'package_amenity_items',
            'amenity_item_id',
            'package_id'
        )->withTimestamps();
    }

    /**
     * Get all booking items for this amenity item
     */
    public function bookingItems(): HasMany
    {
        return $this->hasMany(AmenityBookingItem::class);
    }

    /**
     * Check if item is included in a specific package
     */
    public function isIncludedInPackage(int $packageId): bool
    {
        return PackageAmenityItem::where('package_id', $packageId)
            ->where('amenity_item_id', $this->id)
            ->exists();
    }

    /**
     * Check if item is included in user's active package
     */
    public function isIncludedInUserPackage(int $userId): bool
    {
        $userPackage = UserPackage::where('user_id', $userId)
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>=', now());
            })
            ->first();

        if (!$userPackage) {
            return false;
        }

        return $this->isIncludedInPackage($userPackage->package_id);
    }

    /**
     * Get display price or "Included" status for a user
     */
    public function getPriceForUser(int $userId): array
    {
        $isIncluded = $this->isIncludedInUserPackage($userId);
        
        return [
            'is_included' => $isIncluded,
            'price' => $isIncluded ? 0 : $this->price,
            'display' => $isIncluded ? 'Included' : '+' . number_format($this->price, 2),
        ];
    }

    /**
     * Check if item is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Scope for active items
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for items in a specific amenity
     */
    public function scopeForAmenity($query, int $amenityId)
    {
        return $query->where('amenity_id', $amenityId);
    }
    
    protected function durationText(): Attribute
    {
        return Attribute::get(function () {
            $minutes = (int) $this->duration_minutes;

            if ($minutes < 60) {
                return $minutes . ' minute' . ($minutes !== 1 ? 's' : '');
            }

            $hours = intdiv($minutes, 60);
            $remainingMinutes = $minutes % 60;

            $text = $hours . ' hour' . ($hours !== 1 ? 's' : '');

            if ($remainingMinutes > 0) {
                $text .= ' ' . $remainingMinutes . ' minute' . ($remainingMinutes !== 1 ? 's' : '');
            }

            return $text;
        });
    }
}
