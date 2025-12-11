<?php

namespace App\Models;

use App\Models\BaseModel;

class NotificationRead extends BaseModel
{
    protected $table = 'notification_reads';

    protected $casts = [
        'notification_id' => 'integer',
        'user_id' => 'integer',
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    // Relationships
    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByNotification($query, $notificationId)
    {
        return $query->where('notification_id', $notificationId);
    }

    // Helper methods
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_read ? 'Read' : 'Unread';
    }
}
