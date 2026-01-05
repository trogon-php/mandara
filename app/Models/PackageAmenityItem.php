<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageAmenityItem extends BaseModel
{
    protected $casts = [
        'package_id' => 'integer',
        'amenity_item_id' => 'integer',
    ];

    /**
     * Get the package that owns this association
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(CottagePackage::class, 'package_id');
    }

    /**
     * Get the amenity item that is associated with this package
     */
    public function amenityItem(): BelongsTo
    {
        return $this->belongsTo(AmenityItem::class);
    }

    /**
     * Check if a package includes a specific amenity item
     */
    public static function packageIncludesItem(int $packageId, int $amenityItemId): bool
    {
        return static::where('package_id', $packageId)
            ->where('amenity_item_id', $amenityItemId)
            ->exists();
    }

    /**
     * Get all amenity items included in a package
     */
    public static function getItemsForPackage(int $packageId)
    {
        return static::where('package_id', $packageId)
            ->with('amenityItem')
            ->get()
            ->pluck('amenityItem')
            ->filter();
    }
}
