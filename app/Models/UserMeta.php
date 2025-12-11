<?php

namespace App\Models;

use App\Models\BaseModel;

class UserMeta extends BaseModel
{
    protected $table = 'user_meta';
    protected $casts = [
        'user_id' => 'integer',
        'meta_key' => 'string',
        'meta_value' => 'string'
    ];

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get meta by key
     */
    public function scopeByKey($query, string $key)
    {
        return $query->where('meta_key', $key);
    }

    /**
     * Scope to get meta for specific user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
