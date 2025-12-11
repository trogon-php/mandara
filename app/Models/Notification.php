<?php

namespace App\Models;

use App\Models\BaseModel;

class Notification extends BaseModel
{
    protected $casts = [
        'image' => 'string',
        'course_id' => 'integer',
        'category_id' => 'integer',
        'premium' => 'boolean',
    ];

    protected $fileFields = [
        'image' => [
            'folder' => 'notifications',
            'preset' => 'notifications_image',
            'single' => false, 
            'json' => true,
        ],
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function notificationReads()
    {
        return $this->hasMany(NotificationRead::class, 'notification_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'notification_reads')
                    ->withPivot(['is_read', 'read_at'])
                    ->withTimestamps();
    }

    // Helper methods
    public function markAsReadForUser($userId)
    {
        return $this->notificationReads()->updateOrCreate(
            ['user_id' => $userId],
            [
                'is_read' => true,
                'read_at' => now(),
            ]
        );
    }

    public function markAsUnreadForUser($userId)
    {
        return $this->notificationReads()->updateOrCreate(
            ['user_id' => $userId],
            [
                'is_read' => false,
                'read_at' => null,
            ]
        );
    }

    public function isReadByUser($userId)
    {
        $read = $this->notificationReads()->where('user_id', $userId)->first();
        return $read ? $read->is_read : false;
    }

    public function getReadCountAttribute()
    {
        return $this->notificationReads()->where('is_read', true)->count();
    }

    public function getUnreadCountAttribute()
    {
        return $this->notificationReads()->where('is_read', false)->count();
    }
}

