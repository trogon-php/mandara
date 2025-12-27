<?php

namespace App\Services\Estore;

use App\Models\EstoreOrderItem;
use App\Services\Core\BaseService;

class EstoreOrderItemService extends BaseService
{
    protected string $modelClass = EstoreOrderItem::class;

    public function getFilterConfig(): array
    {
        return [
            'filters' => [
                'order_id' => 'integer',
            ],
            'sort' => [
                'created_at' => 'desc',
            ],
        ];
    }

    public function getSearchFieldsConfig(): array
    {
        return [
            'search' => [
                'order_id' => 'integer',
            ],
        ];
    }

    public function getDefaultSearchFields(): array
    {
        return [
            'order_id' => 'integer',
        ];
    }

    public function getDefaultSorting(): array
    {
        return [
            'created_at' => 'desc',
        ];
    }
}
