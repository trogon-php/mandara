<?php

namespace App\Services\Food;

use App\Models\FoodItem;
use App\Services\Core\BaseService;
use Illuminate\Pagination\LengthAwarePaginator;

class FoodItemService extends BaseService
{
    protected string $modelClass = FoodItem::class;

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
            'category_id' => [
                'type' => 'select',
                'label' => 'Category',
                'col' => 3,
                'options' => $this->getCategoryOptions(),
            ],
            'is_veg' => [
                'type' => 'select',
                'label' => 'Food Type',
                'col' => 3,
                'options' => [
                    '1' => 'Vegetarian',
                    '0' => 'Non-Vegetarian',
                ],
            ],
        ];
    }

    public function getSearchFieldsConfig(): array
    {
        return [
            'title' => 'Title',
            'short_description' => 'Short Description',
        ];
    }

    public function getDefaultSearchFields(): array
    {
        return ['title', 'short_description'];
    }

    public function getDefaultSorting(): array
    {
        return ['field' => 'created_at', 'direction' => 'desc'];
    }

    public function getCategoryOptions(): array
    {
        $categories = app(FoodCategoryService::class)->model->where('status', 1)->orderBy('title')->get();
        $options = ['' => 'All Categories'];
        
        foreach ($categories as $category) {
            $options[$category->id] = $category->title;
        }
        
        return $options;
    }

    public function getActiveItemsPaginated(int $perPage = 15, ?int $categoryId = null, ?bool $isVeg = null): LengthAwarePaginator
    {
        $user = authUser();
        $query = $this->model->active()->with('category');

        // Eager load cart items for the authenticated user
        if ($user) {
            $query->with(['cartItems' => function($q) use ($user) {
                $q->where('user_id', $user->id);
            }]);
        }
        // Filter by categories that are available now (based on start_time and end_time)
        $query->whereHas('category', function($q) {
            $q->active()->availableNow();
        });
        
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($isVeg !== null) {
            $query->where('is_veg', $isVeg);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getItemsForOrdering(?bool $isVeg = null)
    {
        $query = $this->model->active()
            ->inStock()
            ->with('category')
            ->whereHas('category', function($q) {
                $q->active()->availableNow();
            });

        if ($isVeg !== null) {
            $query->where('is_veg', $isVeg);
        }

        return $query->orderBy('sort_order', 'asc')->get();
    }

    public function updateStock(int $itemId, int $quantity): bool
    {
        $item = $this->model->find($itemId);
        if (!$item) {
            return false;
        }
        $item->stock -= $quantity;
        return $item->save();
    }
}