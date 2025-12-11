<?php

namespace App\Services\Cottages;

use App\Models\Cottage;
use App\Services\Core\BaseService;
use App\Services\CottageCategories\CottageCategoryService;

class CottageService extends BaseService
{
    protected string $modelClass = Cottage::class;

    public function __construct(private CottageCategoryService $cottageCategoryService)
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
            'provider' => 'Provider',
        ];
    }

    /**
     * Get default search fields
     */
    public function getDefaultSearchFields(): array
    {
        return ['title', 'provider'];
    }

    /**
     * Get default sorting
     */
    public function getDefaultSorting(): array
    {
        return ['field' => 'sort_order', 'direction' => 'asc'];
    }

    public function store(array $data): Cottage
    {
        $maxSortOrder = $this->model->max('sort_order') ?? 0;
        $data['sort_order'] = $maxSortOrder + 1;

        return parent::store($data);
    }
    public function getCottageCategoryOptions(): array
    {
        return $this->cottageCategoryService->model->active()->sorted()->pluck('title', 'id')->toArray();
    }
    public function getOptions(): array
    {
        return $this->model->active()->sorted()->pluck('title', 'id')->toArray();
    }
}