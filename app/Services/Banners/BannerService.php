<?php

namespace App\Services\Banners;

use App\Models\Banner;
use App\Services\Core\BaseService;
use App\Http\Resources\Banners\AppBannerResource as BannerResource;

class BannerService extends BaseService
{
    protected string $modelClass = Banner::class;

    // Only keep Banner-specific methods that are NOT in BaseService
    
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
            ]
        ];
    }

    public function getSearchFieldsConfig(): array
    {
        return [
            'title' => 'Title',
            'description' => 'Description',
            'action_type' => 'Action Type',
        ];
    }

    public function getDefaultSearchFields(): array
    {
        return ['title', 'description', 'action_value'];
    }

    public function getDefaultSorting(): array
    {
        return ['field' => 'sort_order', 'direction' => 'asc'];
    }

    public function getActiveBanners()
    {
        $banners = $this->model->active()->sorted()->get();
        return BannerResource::collection($banners);
    }

    public function getByActionType(string $actionType)
    {
        return $this->model->byActionType($actionType)->get();
    }
}
