<?php

namespace App\Models;


class CottagePackage extends BaseModel
{
    protected $table = 'cottage_packages';

    protected $casts = [
        'price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'booking_amount' => 'decimal:2',
        'tax_included' => 'boolean',
        'duration_days' => 'integer',
        'sort_order' => 'integer',
    ];

    protected $fillable = [
        'title',
        'description',
        'cottage_category_id',
        'price',
        'discount_amount',
        'booking_amount',
        'tax_included',
        'duration_days',
        'status',
    ];

    public function cottageCategory()
    {
        return $this->belongsTo(CottageCategory::class);
    }

    public function hasDiscount(): bool
    {
        return !is_null($this->discount_amount) && (float) $this->discount_amount > 0;
    }

    /**
     * Effective (payable) base price before tax
     */
    public function getEffectivePriceAttribute(): float
    {
        $discount = $this->discount_amount ?? 0;
        return max(0, (float) $this->price - (float) $discount);
    }

    /**
     * Discount percentage (if any)
     */
    public function getDiscountPercentageAttribute(): ?float
    {
        if (!$this->hasDiscount() || (float) $this->price <= 0) {
            return null;
        }

        return round(((float) $this->discount_amount / (float) $this->price) * 100, 2);
    }

}
