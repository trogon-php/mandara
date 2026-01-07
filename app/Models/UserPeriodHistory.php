<?php

namespace App\Models;

use App\Models\BaseModel;

class UserPeriodHistory extends BaseModel
{
    
    protected $casts = [
        'user_id' => 'integer',
        'start_date' => 'date',
        'period_length' => 'integer',
        'cycle_length' => 'integer',
    ];

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get periods for a user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get latest period first
     */
    public function scopeLatestFirst($query)
    {
        return $query->orderBy('start_date', 'desc');
    }
}
