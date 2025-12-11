<?php

namespace App\Services\FeedCategories;

use App\Models\FeedCategory;
use App\Services\Core\BaseService;
use App\Http\Resources\FeedCategories\AppFeedCategoryCollection;

class FeedCategoryService extends BaseService
{
    protected string $modelClass = FeedCategory::class;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get filter configuration for the admin interface
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
            'created_at' => [
                'type' => 'date-range',
                'label' => 'Date Range',
                'col' => 4,
                'fromField' => 'date_from',
                'toField' => 'date_to',
            ],
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

    // FeedCategory-specific methods
    public function getActiveCategories()
    {
        return $this->model->where('status', 1)->sorted()->get();
    }

    public function getInactiveCategories()
    {
        return $this->model->where('status', 0)->sorted()->get();
    }

    public function getCategoriesWithFeeds()
    {
        return $this->model->with('feeds')->sorted()->get();
    }

    public function getCategoryOptions(): array
    {
        $categories = $this->model->where('status', 1)->sorted()->get();
        $options = ['' => 'All Categories'];
        
        foreach ($categories as $category) {
            $options[$category->id] = $category->title;
        }
        
        return $options;
    }

    public function getAppCategories(): array
    {
        $categories = $this->model->where('status', 1)->sorted()->get();
        return (new AppFeedCategoryCollection($categories))->toArray(request());
    }

    public function getAppCategoriesWithFeeds(): array
    {
        $categories = $this->model->where('status', 1)
            ->with('feeds')
            ->sorted()
            ->get();
        return (new AppFeedCategoryCollection($categories))->toArray(request());
    }

    public function getCategoryById(int $id)
    {
        return $this->model->where('status', 1)->find($id);
    }

    public function getCategoriesByStatus(int $status)
    {
        return $this->model->where('status', $status)->sorted()->get();
    }

    public function getCategoriesCount(): int
    {
        return $this->model->count();
    }

    public function getActiveCategoriesCount(): int
    {
        return $this->model->where('status', 1)->count();
    }

    public function getInactiveCategoriesCount(): int
    {
        return $this->model->where('status', 0)->count();
    }

    public function getCategoriesWithFeedCount()
    {
        return $this->model->withCount('feeds')->sorted()->get();
    }

    public function getMostUsedCategories(int $limit = 5)
    {
        return $this->model->withCount('feeds')
            ->orderBy('feeds_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getLeastUsedCategories(int $limit = 5)
    {
        return $this->model->withCount('feeds')
            ->orderBy('feeds_count', 'asc')
            ->limit($limit)
            ->get();
    }

    public function getEmptyCategories()
    {
        return $this->model->whereDoesntHave('feeds')->sorted()->get();
    }

    public function getCategoriesWithMinimumFeeds(int $minFeeds = 1)
    {
        return $this->model->has('feeds', '>=', $minFeeds)
            ->withCount('feeds')
            ->sorted()
            ->get();
    }

    public function searchCategories(string $search)
    {
        return $this->model->where('title', 'like', "%{$search}%")
            ->sorted()
            ->get();
    }

    public function getCategoriesByDateRange($startDate, $endDate)
    {
        return $this->model->whereBetween('created_at', [$startDate, $endDate])
            ->sorted()
            ->get();
    }

    public function getRecentCategories(int $limit = 10)
    {
        return $this->model->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getOldestCategories(int $limit = 10)
    {
        return $this->model->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();
    }
}
