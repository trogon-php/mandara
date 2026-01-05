<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AmenityBookingItem extends BaseModel
{
    protected $casts = [
        'amenity_booking_id' => 'integer',
        'amenity_item_id' => 'integer',
        'duration_minutes' => 'integer',
        'was_included_in_package' => 'boolean',
        'price' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    /**
     * Get the booking that owns this item
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(AmenityBooking::class, 'amenity_booking_id');
    }

    /**
     * Get the amenity item (current version)
     */
    public function amenityItem(): BelongsTo
    {
        return $this->belongsTo(AmenityItem::class);
    }

    /**
     * Check if this item was included in package
     */
    public function wasIncluded(): bool
    {
        return $this->was_included_in_package === true;
    }

    /**
     * Check if this item was paid for
     */
    public function wasPaid(): bool
    {
        return $this->was_included_in_package === false && $this->price > 0;
    }

    /**
     * Get display price
     */
    public function getDisplayPriceAttribute(): string
    {
        if ($this->was_included_in_package) {
            return 'Included';
        }
        return '+' . number_format($this->price, 2);
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
