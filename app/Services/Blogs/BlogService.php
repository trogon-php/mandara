<?php

namespace App\Services\Blogs;

use App\Http\Resources\Blogs\AppBlogResource;
use App\Models\Blog;
use App\Services\Core\BaseService;

class BlogService extends BaseService
{
    protected string $modelClass = Blog::class;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get filter configuration - used for CRUD filters
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
     * Get search fields configuration for UI
     */
    public function getSearchFieldsConfig(): array
    {
        return [
            'title' => 'Title',
            'slug' => 'Slug',
            'short_description' => 'Short Description',
            'content' => 'Content',
        ];
    }

    /**
     * Get default search fields
     */
    public function getDefaultSearchFields(): array
    {
        return ['title', 'slug', 'short_description', 'content'];
    }

    /**
     * Get default sorting
     */
    public function getDefaultSorting(): array
    {
        return ['field' => 'created_at', 'direction' => 'desc'];
    }

    public function store(array $data): Blog
    {
        $maxSortOrder = $this->model->max('sort_order') ?? 0;
        $data['sort_order'] = $maxSortOrder + 1;

        return parent::store($data);
    }
    // 
    public function getActiveBlogsPaginated($perPage = 10)
    {
        return $this->model->active()
        // ->sorted()
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);
    }
}