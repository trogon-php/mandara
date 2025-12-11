<?php

namespace App\Models;

use App\Models\Traits\HasFileUrls;

class Media extends BaseModel
{
    use HasFileUrls;

    protected $fillable = [
        'name',
        'original_name',
        'file_path',
        'file_type',
        'mime_type',
        'file_size',
        'width',
        'height',
        'folder',
        'alt_text',
        'description',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'sort_order' => 'integer',
        'status' => 'boolean',
    ];

    // File field configuration for URL generation
    public function getFileFields(): array
    {
        return [
            'file_path' => [
                'type' => $this->file_type,
                'single' => true,
            ],
        ];
    }

    // Accessor for file URL
    public function getUrlAttribute(): ?string
    {
        return file_url($this->file_path, $this->file_type);
    }

    // Helper to get human readable file size
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    // // Relationship to admin user
    // public function uploader()
    // {
    //     return $this->belongsTo(\App\Models\Admin::class, 'uploaded_by');
    // }
}
