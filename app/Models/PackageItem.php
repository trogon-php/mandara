<?php

namespace App\Models;

use App\Models\BaseModel;

class PackageItem extends BaseModel
{
    protected $table = 'package_items';

    protected $casts = [
        'package_id' => 'integer',
        'item_id' => 'integer',
        'sort_order' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
    ];

    protected $fillable = [
        'package_id',
        'item_type',
        'item_id',
        'status',
        'sort_order',
    ];

    /**
     * Get the package that owns the item
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Get the course (since we only work with courses now)
     */
    public function course()
    {
        return $this->belongsTo(\App\Models\Course::class, 'item_id');
    }

    /**
     * Get the actual item based on type
     */
    public function item()
    {
        switch ($this->item_type) {
            case 'category':
                return $this->belongsTo(\App\Models\Category::class, 'item_id');
            case 'course':
                return $this->belongsTo(\App\Models\Course::class, 'item_id');
            case 'course_unit':
                return $this->belongsTo(\App\Models\CourseUnit::class, 'item_id');
            case 'course_material':
                return $this->belongsTo(\App\Models\CourseMaterial::class, 'item_id');
            default:
                return null;
        }
    }

    /**
     * Get the status options
     */
    public static function getStatusOptions(): array
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
        ];
    }

    /**
     * Get the item type options
     */
    public static function getItemTypeOptions(): array
    {
        return [
            'category' => 'Category',
            'course' => 'Course',
            'course_unit' => 'Course Unit',
            'course_material' => 'Course Material',
        ];
    }

    /**
     * Get the item title
     */
    public function getItemTitleAttribute(): string
    {
        $item = $this->item;
        if (!$item) {
            return 'Item not found';
        }

        return $item->title ?? $item->name ?? 'Untitled';
    }

    /**
     * Scope for active items
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
