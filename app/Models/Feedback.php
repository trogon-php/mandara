<?php

namespace App\Models;

use App\Models\BaseModel;

class Feedback extends BaseModel
{
    protected $table = 'feedbacks';
    
    protected $casts = [
        'user_id' => 'integer',
        'rating' => 'integer',
        'status' => 'string',
    ];

    protected $fillable = [
        'user_id',
        'message',
        'rating',
        'status',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeReviewed($query)
    {
        return $query->where('status', 'reviewed');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeWithRating($query)
    {
        return $query->whereNotNull('rating');
    }

    public function scopeWithoutRating($query)
    {
        return $query->whereNull('rating');
    }

    // Validation rules for the model
    public static function rules()
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'message' => 'required|string|min:10|max:2000',
            'rating' => 'nullable|integer|min:1|max:5',
            'status' => 'required|in:pending,reviewed,resolved',
        ];
    }

    // Get feedback statistics
    public static function getStatistics()
    {
        return [
            'total' => static::count(),
            'pending' => static::pending()->count(),
            'reviewed' => static::reviewed()->count(),
            'resolved' => static::resolved()->count(),
            'with_rating' => static::withRating()->count(),
            'average_rating' => static::withRating()->avg('rating'),
        ];
    }

    // Get feedback by status
    public static function getByStatus($status)
    {
        return static::where('status', $status)->with('user')->get();
    }

    // Update feedback status
    public function updateStatus($status)
    {
        $this->status = $status;
        return $this->save();
    }
}
