<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Coupon extends BaseModel
{
    protected $casts = [
        'discount_value' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'usage_limit' => 'integer',
        'per_user_limit' => 'integer',
        'status' => 'string',
    ];

    protected $fillable = [
        'code',
        'title',
        'description',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'usage_limit',
        'per_user_limit',
        'status',
    ];

    /**
     * Get the packages associated with this coupon
     */
    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'coupon_packages', 'coupon_id', 'package_id')
            ->withTimestamps();
    }

    /**
     * Get the users who can use this coupon
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'coupon_users', 'coupon_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * Get the usage history for this coupon
     */
    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Get the orders that used this coupon
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Check if coupon is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               $this->start_date <= now() && 
               $this->end_date >= now();
    }

    /**
     * Check if coupon has reached usage limit
     */
    public function hasReachedUsageLimit(): bool
    {
        if (!$this->usage_limit) {
            return false;
        }
        
        return $this->usages()->count() >= $this->usage_limit;
    }

    /**
     * Check if user has reached per-user limit
     */
    public function hasUserReachedLimit(int $userId): bool
    {
        if (!$this->per_user_limit) {
            return false;
        }
        
        return $this->usages()->where('user_id', $userId)->count() >= $this->per_user_limit;
    }

    /**
     * Check if coupon is valid for a specific package
     */
    public function isValidForPackage(int $packageId): bool
    {
        // If no packages are associated, coupon is valid for all packages
        if ($this->packages()->count() === 0) {
            return true;
        }
        
        return $this->packages()->where('package_id', $packageId)->exists();
    }

    /**
     * Check if coupon is valid for a specific user
     */
    public function isValidForUser(int $userId): bool
    {
        // If no users are associated, coupon is valid for all users
        if ($this->users()->count() === 0) {
            return true;
        }
        
        return $this->users()->where('user_id', $userId)->exists();
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount(float $amount): float
    {
        if ($this->discount_type === 'percentage') {
            return ($amount * $this->discount_value) / 100;
        }
        
        return min($this->discount_value, $amount); // Fixed amount, but not more than the total
    }

    /**
     * Scope for active coupons
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    /**
     * Scope for expired coupons
     */
    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now());
    }
}


