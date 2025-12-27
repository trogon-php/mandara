<?php

namespace App\Services\Estore;

use App\Models\EstoreProduct;
use App\Services\Core\BaseService;
use Illuminate\Pagination\LengthAwarePaginator;

class EstoreProductService extends BaseService
{
    protected string $modelClass = EstoreProduct::class;

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
            'is_featured' => [
                'type' => 'select',
                'label' => 'Featured',
                'col' => 3,
                'options' => [
                    '1' => 'Yes',
                    '0' => 'No',
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
        $categories = app(EstoreCategoryService::class)->model->where('status', 1)->orderBy('title')->get();
        $options = ['' => 'All Categories'];
        
        foreach ($categories as $category) {
            $options[$category->id] = $category->title;
        }
        
        return $options;
    }

    public function getActiveProductsPaginated(int $perPage = 15, ?int $categoryId = null, ?bool $featured = null): LengthAwarePaginator
    {
        $query = $this->model->active()->with('category');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($featured !== null) {
            $query->where('is_featured', $featured ? 1 : 0);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getProductForApi(int $id): ?EstoreProduct
    {
        return $this->model->active()->with('category')->find($id);
    }

    public function getFeaturedProducts(int $limit = 10)
    {
        return $this->model->active()->featured()->with('category')->limit($limit)->get();
    }
    
    // Update stock
    public function updateStock(int $productId, int $quantity): bool
    {
        $product = $this->model->find($productId);
        if(!$product) {
            return false;
        }
        $product->stock -= $quantity;
        if($product->save()) {
            return true;
        }
        return false;
    }
}