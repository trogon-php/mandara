<?php

namespace App\Services\GalleryImages;

use App\Models\GalleryImage;
use App\Models\GalleryAlbum;
use App\Services\Core\BaseService;

class GalleryImageService extends BaseService
{
    protected string $modelClass = GalleryImage::class;

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
            'album_id' => [
                'type' => 'exact',
                'label' => 'Album',
                'col' => 3,
                'options' => $this->getAlbumOptions(),
            ]
        ];
    }

    public function getSearchFieldsConfig(): array
    {
        return [
            'title' => 'Title',
            'description' => 'Description',
            'album.title' => 'Album Title',
        ];
    }

    public function getDefaultSearchFields(): array
    {
        return ['title', 'description', 'album.title'];
    }

    public function getDefaultSorting(): array
    {
        return ['field' => 'sort_order', 'direction' => 'asc'];
    }

    /**
     * Get album options for filter dropdown
     */
    public function getAlbumOptions(): array
    {
        return GalleryAlbum::pluck('title', 'id')->toArray();
    }

    /**
     * Get images with album relationship
     */
    public function getImagesWithAlbum()
    {
        return $this->model->with('album')->get();
    }

    /**
     * Get images for a specific album
     */
    public function getImagesByAlbum($albumId)
    {
        return $this->model->where('album_id', $albumId)->get();
    }

    /**
     * Get active images for a specific album
     */
    public function getActiveImagesByAlbum($albumId)
    {
        return $this->model->where('album_id', $albumId)
            ->where('status', 1)
            ->get();
    }
}
