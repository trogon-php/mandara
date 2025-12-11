<?php

namespace App\Models;

use App\Models\BaseModel;

class PackageFeature extends BaseModel
{
    protected $table = 'package_features';

    protected $casts = [
        'package_id' => 'integer',
        'sort_order' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
    ];

    protected $fillable = [
        'package_id',
        'title',
        'description',
        'status',
        'sort_order',
    ];

    /**
     * Get the status options for the package feature
     */
    public static function getStatusOptions(): array
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
        ];
    }

    /**
     * Scope to get active package features
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get the package that owns this feature
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}

