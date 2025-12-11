<?php

namespace App\Services\Qa;

use App\Models\QaCategory;
use App\Services\Core\BaseService;
use App\Services\Traits\CacheableService;
use Illuminate\Support\Collection;

class QaCategoryService extends BaseService
{
    use CacheableService;
    
    protected string $cachePrefix = 'qa_categories';
    protected int $cacheTtl = 3600;
    protected string $modelClass = QaCategory::class;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get filter configuration for admin
     */
    public function getFilterConfig(): array
    {
        return [
            'status' => [
                'type' => 'select',
                'label' => 'Status',
                'col' => 3,
                'options' => [
                    '1' => 'Active',
                    '0' => 'Inactive',
                ],
            ],
        ];
    }

    /**
     * Get search fields configuration
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

    // ==================== API METHODS ====================

    /**
     * Get all active categories for API
     */
    public function getActiveCategories()
    {
        return $this->remember('active:all', function () {
            return $this->model
                ->where('status', 1)
                ->withCount('questions')
                ->orderBy('sort_order', 'asc')
                ->get();
        });
    }

    /**
     * Get single category with question count for API
     */
    public function getCategoryForApi(int $categoryId): ?QaCategory
    {
        return $this->model
            ->where('id', $categoryId)
            ->where('status', 1)
            ->withCount('qaQuestions')
            ->first();
    }

    /**
     * Get categories with question counts for API
     */
    public function getCategoriesWithCounts(): Collection
    {
        return $this->remember('with_counts', function () {
            return $this->model
                ->where('status', 1)
                ->withCount(['qaQuestions' => function($query) {
                    $query->where('status', 1);
                }])
                ->orderBy('sort_order', 'asc')
                ->get();
        });
    }
}
