<?php

namespace App\Services\Feeds;

use App\Models\Feed;
use App\Models\FeedCategory;
use App\Services\Core\BaseService;
use App\Http\Resources\Feeds\AppFeedCollection;
use App\Http\Resources\Feeds\AppFeedResource;
use App\Services\Traits\CacheableService;

class FeedService extends BaseService
{
    // CacheableService trait
    use CacheableService;
    protected string $cachePrefix = 'feeds';
    protected int $cacheTtl = 1800;

    protected string $modelClass = Feed::class;

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
                'col' => 2,
                'options' => [
                    '1' => 'Active',
                    '0' => 'Inactive',
                ],
            ],
            'feed_category_id' => [
                'type' => 'select',
                'label' => 'Category',
                'col' => 2,
                'options' => $this->getCategoryOptions(),
            ],
            'created_at' => [
                'type' => 'date-range',
                'label' => 'Date Range',
                'col' => 6,
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
            'content' => 'Content',
        ];
    }

    /**
     * Get default search fields
     */
    public function getDefaultSearchFields(): array
    {
        return ['title', 'content'];
    }

    /**
     * Get default sorting
     */
    public function getDefaultSorting(): array
    {
        return ['field' => 'sort_order', 'direction' => 'asc'];
    }

    /**
     * Get category options for filter dropdown
     */
    private function getCategoryOptions(): array
    {
        $categories = FeedCategory::where('status', 1)->orderBy('title')->get();
        $options = ['' => 'All Categories'];
        
        foreach ($categories as $category) {
            $options[$category->id] = $category->title;
        }
        
        return $options;
    }

    public function getLatestForUser(int $userId, int $limit = 10)
    {
        return $this->remember("user:{$userId}:latest", function () use ($userId, $limit) {
            return $this->model->where('status', 1)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    // Feed-specific methods
    public function getActiveFeeds()
    {
        return $this->model->where('status', 1)->sorted()->get();
    }

    public function getInactiveFeeds()
    {
        return $this->model->where('status', 0)->sorted()->get();
    }

    public function getFeedsByCategory(int $categoryId)
    {
        return $this->model->where('feed_category_id', $categoryId)
            ->where('status', 1)
            ->sorted()
            ->get();
    }

    public function getFeedsByCourse(int $courseId)
    {
        return $this->model->where('course_id', $courseId)
            ->where('status', 1)
            ->sorted()
            ->get();
    }

    public function getFeedsWithCategory()
    {
        return $this->model->with('feedCategory')->sorted()->get();
    }

    public function getFeedsWithCourse()
    {
        return $this->model->with('course')->sorted()->get();
    }

    public function getFeedsWithRelations()
    {
        return $this->model->with(['feedCategory', 'course'])->sorted()->get();
    }

    public function getAppFeeds(): array
    {
        $feeds = $this->model->where('status', 1)->sorted()->get();
        return (new AppFeedCollection($feeds))->toArray(request());
    }

    public function getAppFeedsByCategory(int $categoryId): array
    {
        $feeds = $this->model->where('status', 1)
            ->where('feed_category_id', $categoryId)
            ->sorted()
            ->get();
        return (new AppFeedCollection($feeds))->toArray(request());
    }

    public function getAppFeedsByCourse(int $courseId): array
    {
        $feeds = $this->model->where('status', 1)
            ->where('course_id', $courseId)
            ->sorted()
            ->get();
        return (new AppFeedCollection($feeds))->toArray(request());
    }

    public function getAppFeed(int $id): array
    {
        $feed = $this->model->where('status', 1)
            ->with(['feedCategory', 'course'])
            ->find($id);
        
        if (!$feed) {
            return [];
        }
        
        return (new AppFeedResource($feed))->toArray(request());
    }

    public function getRecentFeeds(int $limit = 10)
    {
        return $this->model->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getPopularFeeds(int $limit = 10)
    {
        // Assuming you have a views_count or similar field
        return $this->model->where('status', 1)
            ->orderBy('views_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getFeedsByDateRange($startDate, $endDate)
    {
        return $this->model->where('status', 1)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sorted()
            ->get();
    }

    public function getFeedsByType(string $type)
    {
        return $this->model->where('status', 1)
            ->where('type', $type)
            ->sorted()
            ->get();
    }

    public function getVideoFeeds()
    {
        return $this->model->where('status', 1)
            ->whereNotNull('feed_video')
            ->sorted()
            ->get();
    }

    public function getImageFeeds()
    {
        return $this->model->where('status', 1)
            ->whereNotNull('feed_image')
            ->sorted()
            ->get();
    }

    public function getTextFeeds()
    {
        return $this->model->where('status', 1)
            ->whereNull('feed_video')
            ->whereNull('feed_image')
            ->sorted()
            ->get();
    }

    public function searchFeeds(string $search)
    {
        return $this->model->where('status', 1)
            ->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
            })
            ->sorted()
            ->get();
    }

    public function getFeedsCount(): int
    {
        return $this->model->count();
    }

    public function getActiveFeedsCount(): int
    {
        return $this->model->where('status', 1)->count();
    }

    public function getInactiveFeedsCount(): int
    {
        return $this->model->where('status', 0)->count();
    }

    public function getFeedsByCategoryCount(int $categoryId): int
    {
        return $this->model->where('feed_category_id', $categoryId)->count();
    }

    public function getFeedsByCourseCount(int $courseId): int
    {
        return $this->model->where('course_id', $courseId)->count();
    }

    public function getFeedsWithFileCount()
    {
        return $this->model->where('status', 1)
            ->where(function ($query) {
                $query->whereNotNull('feed_image')
                      ->orWhereNotNull('feed_video');
            })
            ->count();
    }

    public function getEmptyFeeds()
    {
        return $this->model->where('status', 1)
            ->whereNull('feed_image')
            ->whereNull('feed_video')
            ->sorted()
            ->get();
    }

    public function getFeedsWithMinimumFiles(int $minFiles = 1)
    {
        return $this->model->where('status', 1)
            ->where(function ($query) {
                $query->whereNotNull('feed_image')
                      ->orWhereNotNull('feed_video');
            })
            ->sorted()
            ->get();
    }

    public function getFeedsByUser(int $userId)
    {
        return $this->model->where('created_by', $userId)
            ->sorted()
            ->get();
    }

    public function getFeedsByUserCount(int $userId): int
    {
        return $this->model->where('created_by', $userId)->count();
    }

    public function getMostUsedCategories(int $limit = 5)
    {
        return $this->model->selectRaw('feed_category_id, COUNT(*) as feeds_count')
            ->where('status', 1)
            ->groupBy('feed_category_id')
            ->orderBy('feeds_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getFeedsStatistics()
    {
        return [
            'total' => $this->getFeedsCount(),
            'active' => $this->getActiveFeedsCount(),
            'inactive' => $this->getInactiveFeedsCount(),
            'with_files' => $this->getFeedsWithFileCount(),
            'empty' => $this->getEmptyFeeds()->count(),
        ];
    }
}