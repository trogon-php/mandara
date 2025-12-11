<?php

namespace App\Models;

use App\Models\BaseModel;

class GalleryImage extends BaseModel
{
    protected $table = 'gallery_images';

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
        'album_id' => 'integer',
    ];

    protected $fileFields = [
        'image' => [
            'folder' => 'gallery/images',
            'preset' => 'gallery_image',
            'single' => true,
        ],
    ];

    /**
     * Get the album that owns this image
     */
    public function album()
    {
        return $this->belongsTo(GalleryAlbum::class, 'album_id');
    }

    /**
     * Scope for active images
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope for images in a specific album
     */
    public function scopeInAlbum($query, $albumId)
    {
        return $query->where('album_id', $albumId);
    }
}