<?php

namespace App\Models;


class UserDevice extends BaseModel
{
    protected $table = 'user_devices';

    protected $casts = [
        'user_id' => 'integer',
        'device_id' => 'string',
        'fcm_token' => 'string',
        'platform' => 'string',
        'device_name' => 'string',
        'app_version' => 'string',
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get active devices
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get devices by platform
     */
    public function scopeByPlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Scope to get devices for user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Mark device as active
     */
    public function markAsActive(): void
    {
        $this->update([
            'is_active' => true,
            'last_used_at' => now(),
        ]);
    }

    /**
     * Mark device as inactive
     */
    public function markAsInactive(): void
    {
        $this->update([
            'is_active' => false,
        ]);
    }
}
