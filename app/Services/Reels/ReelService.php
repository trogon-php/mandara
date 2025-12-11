<?php

namespace App\Services\Reels;

use App\Models\Reel;
use App\Models\ReelCategory;
use App\Models\Course;
use App\Models\Category;
use App\Services\Core\BaseService;
use App\Http\Resources\Reels\AppReelResource;

class ReelService extends BaseService
{
    protected string $modelClass = Reel::class;

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
            'premium' => [
                'type' => 'exact',  
                'label' => 'Content Type',
                'col' => 3,
                'options' => [
                    '1' => 'Premium',
                    '0' => 'Free',
                ],
            ],
            'reel_category_id' => [
                'type' => 'exact',  
                'label' => 'Reel Category',
                'col' => 3,
                'options' => $this->getReelCategoryOptions(),
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
        return ['title', 'description'];
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
    public function getReelCategoryOptions(): array
    {
        return ReelCategory::active()
            ->sorted()
            ->pluck('title', 'id')
            ->toArray();
    }

    /**
     * Get course options for filters
     */
    public function getCourseOptions(): array
    {
        return Course::published()
            ->sorted()
            ->pluck('title', 'id')
            ->toArray();
    }

    /**
     * Get category options for filters
     */
    public function getCategoryOptions(): array
    {
        return Category::active()
            ->sorted()
            ->pluck('title', 'id')
            ->toArray();
    }

    /**
     * Get app reels for API
     */
    public function getAppReels($limit = 10): array
    {
        $reels = $this->model
            ->active()
            ->sorted()
            ->with(['reelCategory'])
            ->limit($limit)
            ->get();
        return AppReelResource::collection($reels)->toArray(request());
    }

    /**
     * Get premium reels
     */
    public function getPremiumReels()
    {
        return $this->model
            ->premium()
            ->active()
            ->sorted()
            ->get();
    }

    /**
     * Get free reels
     */
    public function getFreeReels()
    {
        return $this->model
            ->free()
            ->active()
            ->sorted()
            ->get();
    }

    /**
     * Get reels by reel category
     */
    public function getReelsByReelCategory(int $reelCategoryId)
    {
        return $this->model
            ->byReelCategory($reelCategoryId)
            ->active()
            ->sorted()
            ->get();
    }

    /**
     * Get reels by course
     */
    public function getReelsByCourse(int $courseId)
    {
        return $this->model
            ->byCourse($courseId)
            ->active()
            ->sorted()
            ->get();
    }

    /**
     * Get reels by category
     */
    public function getReelsByCategory(int $categoryId)
    {
        return $this->model
            ->byCategory($categoryId)
            ->active()
            ->sorted()
            ->get();
    }
}
