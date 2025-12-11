<?php

namespace App\Models;

use App\Models\BaseModel;

class Banner extends BaseModel
{
    protected $table = 'banners';

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
        'action_type' => 'string',
    ];

    protected $fileFields = [
        'image' => [
            'folder' => 'banners',
            'preset' => 'banners_image',
            'single' => true,
        ],
    ];

    /**
     * Get the course relationship for course action type
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'action_value', 'id');
    }

    /**
     * Scope for active banners
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope for banners by action type
     */
    public function scopeByActionType($query, string $actionType)
    {
        return $query->where('action_type', $actionType);
    }

    /**
     * Get the action URL based on action type
     */
    public function getActionUrlAttribute(): ?string
    {
        switch ($this->action_type) {
            case 'video':
            case 'link':
                return $this->action_value;
            case 'course':
                return $this->course ? route('courses.show', $this->course->id) : null;
            case 'text':
                return null; // No URL for text type
            default:
                return null;
        }
    }

    /**
     * Get the action display text
     */
    public function getActionDisplayAttribute(): string
    {
        switch ($this->action_type) {
            case 'video':
                return 'Play Video';
            case 'link':
                return 'Visit Link';
            case 'course':
                return $this->course ? 'View Course: ' . $this->course->title : 'View Course';
            case 'text':
                return 'Read More';
            default:
                return '';
        }
    }
}
