<?php

namespace App\Services\GalleryAlbums;

use App\Models\GalleryAlbum;
use App\Services\Core\BaseService;

class GalleryAlbumService extends BaseService
{
    protected string $modelClass = GalleryAlbum::class;

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

    public function getSearchFieldsConfig(): array
    {
        return [
            'title' => 'Title',
            'description' => 'Description',
        ];
    }

    public function getDefaultSearchFields(): array
    {
        return ['title', 'description'];
    }

    public function getDefaultSorting(): array
    {
        return ['field' => 'sort_order', 'direction' => 'asc'];
    }

    /**
     * Get albums with their image counts
     */
    public function getAlbumsWithImageCounts()
    {
        return $this->model->withCount('images')->get();
    }

    /**
     * Get active albums with their active image counts
     */
    public function getActiveAlbumsWithImageCounts()
    {
        return $this->model->active()
            ->withCount(['images' => function ($query) {
                $query->where('status', 1);
            }])
            ->get();
    }
}
