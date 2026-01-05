<?php

namespace App\Services\Food;

use App\Models\FoodMenu;
use App\Services\Core\BaseService;
use Carbon\Carbon;

class FoodMenuService extends BaseService
{
    protected string $modelClass = FoodMenu::class;

    public function getFilterConfig(): array
    {
        return [
            'menu_date' => [
                'type' => 'date',
                'label' => 'Menu Date',
                'col' => 3,
            ],
        ];
    }

    public function getSearchFieldsConfig(): array
    {
        return [
            'menu_date' => 'Menu Date',
        ];
    }

    public function getDefaultSearchFields(): array
    {
        return ['menu_date'];
    }

    public function getDefaultSorting(): array
    {
        return ['field' => 'menu_date', 'direction' => 'desc'];
    }

    public function getMenuByDate(string $date, ?bool $isVeg = null)
    {
        $query = $this->model->byDate($date)
            ->with(['item.category'])
            ->whereHas('item', function($q) use ($isVeg) {
                $q->active();
                if ($isVeg !== null) {
                    $q->where('is_veg', $isVeg);
                }
            })
            ->orderBy('sort_order', 'asc');

        return $query->get();
    }

    public function getMenuGroupedByCategory(string $date, ?bool $isVeg = null)
    {
        $menuItems = $this->getMenuByDate($date, $isVeg);
        
        return $menuItems->groupBy(function($menuItem) {
            return $menuItem->item->category_id;
        })->map(function($items, $categoryId) {
            $category = $items->first()->item->category;
            return [
                'category' => $category,
                'items' => $items->map(function($menuItem) {
                    return $menuItem->item;
                })
            ];
        })->values();
    }

    public function addItemToMenu(int $itemId, string $date, ?int $sortOrder = null): FoodMenu
    {
        return $this->model->updateOrCreate(
            [
                'food_item_id' => $itemId,
                'menu_date' => $date,
            ],
            [
                'sort_order' => $sortOrder ?? $this->getNextSortOrder($date),
            ]
        );
    }

    public function removeItemFromMenu(int $itemId, string $date): bool
    {
        return $this->model->where('food_item_id', $itemId)
            ->where('menu_date', $date)
            ->delete();
    }

    protected function getNextSortOrder(string $date): int
    {
        $maxOrder = $this->model->where('menu_date', $date)->max('sort_order');
        return ($maxOrder ?? 0) + 1;
    }
}