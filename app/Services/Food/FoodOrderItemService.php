<?php

namespace App\Services\Food;

use App\Models\FoodOrderItem;
use App\Services\Core\BaseService;

class FoodOrderItemService extends BaseService
{
    protected string $modelClass = FoodOrderItem::class;

    public function getFilterConfig(): array
    {
        return [
            'order_id' => [
                'type' => 'integer',
                'label' => 'Order ID',
            ],
        ];
    }

    public function getSearchFieldsConfig(): array
    {
        return [
            'item_title' => 'Item Title',
        ];
    }

    public function getDefaultSearchFields(): array
    {
        return ['item_title'];
    }

    public function getDefaultSorting(): array
    {
        return ['field' => 'created_at', 'direction' => 'desc'];
    }
}