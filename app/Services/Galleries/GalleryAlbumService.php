<?php

namespace App\Services\Galleries;

use App\Models\GalleryAlbum;
use App\Models\GalleryImage;
use App\Models\Course;
use App\Models\Category;
use App\Services\Core\BaseService;
use App\Http\Resources\Galleries\AppGalleryResource;

class GalleryService extends BaseService
{
    protected string $modelClass = GalleryAlbum::class;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get filter configuration
     */
    public function getFilterConfig(): array
    {
        return [
            'status' => [
                'type' => 'exact',  
                'label' => 'Status',
                'col' => 3,
                'options' => [
                    '1' => 'Active',
                    '0' => 'Inactive',
                ],
            ]
        ];
    }

    /**
     * Get search fields configuration for UI
     */
    public function getSearchFieldsConfig(): array
    {
        return [
            'title' => 'Title'
        ];
    }

    /**
     * Get default search fields
     */
    public function getDefaultSearchFields(): array
    {
        return ['title'];
    }

    /**
     * Get default sorting
     */
    public function getDefaultSorting(): array
    {
        return ['field' => 'sort_order', 'direction' => 'asc'];
    }

    /**
     * Get app reels for API
     */
    public function getAppGalleryAlbums($limit = 10): array
    {
        $galleryAlbums = $this->model
            ->active()
            ->sorted()
            ->with(['galleryImages'])
            ->limit($limit)
            ->get();
        return AppGalleryResource::collection($galleryAlbums)->toArray(request());
    }
}
