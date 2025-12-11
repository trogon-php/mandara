<?php

namespace App\Services\Galleries;

use App\Models\GalleryImage;
use App\Models\GalleryAlbum;
use App\Services\Core\BaseService;
use App\Http\Resources\Galleries\AppGalleryImageResource;

class GalleryImageService extends BaseService
{
    protected string $modelClass = GalleryImage::class;

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
            ],
            'gallery_album_id' => [
                'type' => 'exact',  
                'label' => 'Gallery Album',
                'col' => 3,
                'options' => $this->getGalleryAlbumOptions(),
            ]
        ];
    }

    /**
     * Get search fields configuration for UI
     */
    public function getSearchFieldsConfig(): array
    {
        return [
            'title' => 'Title',
            'description' => 'Description',
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
     * Get reel category options for filters
     */
    public function getGalleryAlbumOptions(): array
    {
        return GalleryAlbum::active()
            ->sorted()
            ->pluck('title', 'id')
            ->toArray();
    }

    /**
     * Get active gallery images with album relationship
     */
    public function getActiveGalleryImages(int $limit = 10)
    {
        $galleryImages = $this->model
            ->active()
            ->sorted()
            ->with(['album'])
            ->limit($limit)
            ->get();
        return AppGalleryImageResource::collection($galleryImages)->toArray(request());
    }

    /**
     * Get gallery images by album
     */
    public function getGalleryImagesByAlbum(int $albumId)
    {
        return $this->model
            ->inAlbum($albumId)
            ->active()
            ->sorted()
            ->get();
    }

    /**
     * Get app gallery images (for mobile app)
     */
    public function getAppGalleryImages(int $limit = 10): array
    {
        $galleryImages = $this->model
            ->active()
            ->sorted()
            ->with(['album'])
            ->limit($limit)
            ->get();
        return AppGalleryImageResource::collection($galleryImages)->toArray(request());
    }

    /**
     * Get paginated gallery images for API
     */
    public function paginate(int $perPage = 10, array $columns = ['*']): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->model
            ->active()
            ->sorted()
            ->with(['album'])
            ->paginate($perPage, $columns);
    }
}
