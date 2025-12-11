<?php

namespace App\Models;

use App\Models\BaseModel;

class GalleryAlbum extends BaseModel
{
    protected $table = 'gallery_albums';

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $fileFields = [
        'thumbnail' => [
            'folder' => 'gallery/albums',
            'preset' => 'gallery_thumbnail',
            'single' => true,
        ],
    ];

    /**
     * Get the images for this album
     */
    public function images()
    {
        return $this->hasMany(GalleryImage::class, 'album_id');
    }

    /**
     * Get active images for this album
     */
    public function activeImages()
    {
        return $this->hasMany(GalleryImage::class, 'album_id')->where('status', 1);
    }

    /**
     * Scope for active albums
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Get the album's image count
     */
    public function getImageCountAttribute(): int
    {
        return $this->images()->count();
    }

    /**
     * Get the album's active image count
     */
    public function getActiveImageCountAttribute(): int
    {
        return $this->activeImages()->count();
    }
}