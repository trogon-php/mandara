<?php

namespace App\Services\DietPlans;

use App\Models\DietPlan;
use App\Services\Core\BaseService;
use Illuminate\Support\Str;

class DietPlanService extends BaseService
{
    protected string $modelClass = DietPlan::class;

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

    public function store(array $data): DietPlan
    {
        $maxSortOrder = $this->model->max('sort_order') ?? 0;
        $data['sort_order'] = $maxSortOrder + 1;

        return parent::store($data);
    }
    public function getActiveDietPlansPaginated(?string $month, int $perPage = 10)
    {
        $query = $this->model->active();
        if ($month) {
            $query->where('month', $month);
        }
        return $query->sorted()->paginate($perPage);
    }
}