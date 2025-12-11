<?php

namespace App\Models;

use App\Models\BaseModel;

class Package extends BaseModel
{
    protected $table = 'packages';

    protected $casts = [
        'price' => 'decimal:2',
        'offer_price' => 'decimal:2',
        'duration_days' => 'integer',
        'expire_date' => 'date',
        'sort_order' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
    ];

    protected $fillable = [
        'title',
        'description',
        'price',
        'offer_price',
        'duration_days',
        'expire_date',
        'status',
        'system_generated',
        'sort_order',
    ];

    /**
     * Get the status options for the package
     */
    public static function getStatusOptions(): array
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
        ];
    }

    /**
     * Scope to get active packages
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get packages with offer price
     */
    public function scopeWithOffer($query)
    {
        return $query->whereNotNull('offer_price');
    }

    /**
     * Get the effective price (offer price if available, otherwise regular price)
     */
    public function getEffectivePriceAttribute(): float
    {
        return $this->offer_price ?? $this->price;
    }

    /**
     * Check if package has an offer
     */
    public function hasOffer(): bool
    {
        return !is_null($this->offer_price);
    }

    /**
     * Check if package is expired
     */
    public function isExpired(): bool
    {
        if (is_null($this->expire_date)) {
            return false;
        }
        
        return $this->expire_date->isPast();
    }

    /**
     * Get the discount percentage if offer is available
     */
    public function getDiscountPercentageAttribute(): ?float
    {
        if (!$this->hasOffer()) {
            return null;
        }

        return round((($this->price - $this->offer_price) / $this->price) * 100, 2);
    }

    /**
     * Get the package items
     */
    public function items()
    {
        return $this->hasMany(PackageItem::class)->orderBy('sort_order');
    }

    /**
     * Get the package features
     */
    public function features()
    {
        return $this->hasMany(PackageFeature::class)->orderBy('sort_order');
    }

    /**
     * Get the coupons associated with this package
     */
    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_packages', 'package_id', 'coupon_id')
            ->withTimestamps();
    }

    /**
     * Get expiry title
     */
    public function getExpiryTextAttribute(): string
    {
        return "You will access this package until " . $expiryDate->format('Y-m-d');
    }
}
