<?php

namespace App\Services\Estore;

use App\Models\EstoreCategory;
use App\Services\Core\BaseService;

class EstoreCategoryService extends BaseService
{
    protected string $modelClass = EstoreCategory::class;

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

    public function getActiveCategories()
    {
        return $this->model->active()->sorted()->get();
    }
}