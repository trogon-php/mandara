<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Estore\AppEstoreCategoryResource;
use App\Http\Resources\Estore\AppEstoreProductResource;
use App\Services\Estore\EstoreCartService;
use App\Services\Estore\EstoreCategoryService;
use App\Services\Estore\EstoreOrderService;
use App\Services\Estore\EstoreProductService;
use Illuminate\Http\Request;

class EstoreController extends BaseApiController
{
    public function __construct(
        protected EstoreProductService $estoreProductService,
        protected EstoreCategoryService $estoreCategoryService,
        protected EstoreCartService $estoreCartService,
        protected EstoreOrderService $estoreOrderService,
    )
    {
        parent::__construct();
    }

    public function getCategories(Request $request)
    {
        $categories = $this->estoreCategoryService->getActiveCategories();
        $categories = AppEstoreCategoryResource::collection($categories);
        
        return $this->respondSuccess($categories, 'Categories fetched successfully');
    }
    
    public function getProducts(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $categoryId = $request->get('category_id');
        $featured = $request->get('featured') ? true : null;

        $products = $this->estoreProductService->getActiveProductsPaginated($perPage, $categoryId, $featured);
        // dd($products);
        $request->merge(['list' => true]);
        $products = AppEstoreProductResource::collection($products);

        return $this->respondPaginated($products, 'Products retrieved successfully');
    }

    public function getProduct(Request $request, string $id)
    {
        $product = $this->estoreProductService->getProductForApi($id);
        if(!$product){
            return $this->respondError('Product not found', 404);
        }
        $product = AppEstoreProductResource::make($product);

        return $this->respondSuccess($product, 'Product retrieved successfully');
    }
}
