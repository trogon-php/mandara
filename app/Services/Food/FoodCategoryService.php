<?php

namespace App\Services\Food;

use App\Models\FoodCategory;
use App\Services\Core\BaseService;

class FoodCategoryService extends BaseService
{
    protected string $modelClass = FoodCategory::class;

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

    public function getAvailableCategories()
    {
        return $this->model->active()->availableNow()->sorted()->get();
    }
    public function getAppCategories()
    {
        $categories = $this->model->active()->availableNow()->sorted()->get();

        $categories = $categories->map(function($category) {
            return [
                'id' => $category->id,
                'title' => $category->title,
            ];
        });

        return $categories;
    }
}