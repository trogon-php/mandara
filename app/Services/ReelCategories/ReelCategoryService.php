<?php

namespace App\Services\ReelCategories;

use App\Models\ReelCategory;
use App\Services\Core\BaseService;
use App\Http\Resources\ReelCategories\AppReelCategoryResource;

class ReelCategoryService extends BaseService
{
    protected string $modelClass = ReelCategory::class;

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
            'title' => 'Title',
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
     * Get app reel categories for API
     */
    public function getAppReelCategories(): array
    {
        $categories = $this->model
            ->active()
            ->sorted()
            ->get();
        return AppReelCategoryResource::collection($categories)->toArray(request());
    }

    /**
     * Get app reel categories with reels
     */
    public function getAppReelCategoriesWithReels()
    {
        $categories = $this->model
            ->with('activeReels')
            ->active()
            ->sorted()
            ->get();
    
        return AppReelCategoryResource::collection($categories);
    }

    // ReelCategory-specific methods
    public function getActiveReelCategories()
    {
        return $this->model->active()->sorted()->get();
    }

    public function getInactiveReelCategories()
    {
        return $this->model->where('status', 0)->sorted()->get();
    }

    /**
     * Get reel categories with reel count
     */
    public function getReelCategoriesWithCount()
    {
        return $this->model
            ->withCount('reels')
            ->active()
            ->sorted()
            ->get();
    }

    /**
     * Get reel categories that have reels
     */
    public function getReelCategoriesWithReels()
    {
        return $this->model
            ->whereHas('reels')
            ->active()
            ->sorted()
            ->get();
    }
}
