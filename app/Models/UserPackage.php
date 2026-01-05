<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPackage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'package_id',
        'order_id',
        'status',
        'expiry_date',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    /**
     * Get the user that owns the package
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the package associated with the user package
     */
    public function package()
    {
        return $this->belongsTo(CottagePackage::class);
    }

    /**
     * Get the order associated with the user package
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user who created this user package
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this user package
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who deleted this user package
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by package
     */
    public function scopeByPackage($query, $packageId)
    {
        return $query->where('package_id', $packageId);
    }

    /**
     * Check if the package is expired
     */
    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Check if the package is active
     */
    public function isActive()
    {
        return $this->status === 'active' && !$this->isExpired();
    }
}
