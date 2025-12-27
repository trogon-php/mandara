<?php

namespace App\Services\MealPackages;

use App\Models\MealPackage;
use App\Services\Core\BaseService;

class MealPackageService extends BaseService
{
    protected string $modelClass = MealPackage::class;

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
            'is_veg' => [
                'type' => 'select',
                'label' => 'Type',
                'col' => 3,
                'options' => [
                    '1' => 'Vegetarian',
                    '0' => 'Non-Vegetarian',
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
            'labels' => 'Labels',
        ];
    }

    /**
     * Get default search fields
     */
    public function getDefaultSearchFields(): array
    {
        return ['title', 'labels'];
    }

    /**
     * Get default sorting
     */
    public function getDefaultSorting(): array
    {
        return ['field' => 'sort_order', 'direction' => 'asc'];
    }

    public function store(array $data): MealPackage
    {
        $maxSortOrder = $this->model->max('sort_order') ?? 0;
        $data['sort_order'] = $maxSortOrder + 1;
        $data['is_veg'] = isset($data['is_veg']) ? (int)$data['is_veg'] : 0;

        return parent::store($data);
    }

    public function update(int $id, array $data): ?MealPackage
    {
        if (isset($data['is_veg'])) {
            $data['is_veg'] = (int)$data['is_veg'];
        }
        return parent::update($id, $data);
    }
}