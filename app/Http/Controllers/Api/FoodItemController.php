<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Food\AppFoodItemResource;
use App\Services\Food\FoodCategoryService;
use App\Services\Food\FoodItemService;
use Illuminate\Http\Request;

class FoodItemController extends BaseApiController
{
    public function __construct(
        protected FoodItemService $foodItemService,
        protected FoodCategoryService $foodCategoryService
    ) {}

    public function getFoodCategories(Request $request)
    {
        $foodCategories = $this->foodCategoryService->getAppCategories();
        return $this->respondSuccess($foodCategories, 'Food categories fetched successfully');
    }
    
    public function getFoodItems(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $categoryId = $request->get('category_id');

        $foodItems = $this->foodItemService->getActiveItemsPaginated($perPage, $categoryId);
        $foodItems = AppFoodItemResource::collection($foodItems);
        
        return $this->respondSuccess($foodItems, 'Food items fetched successfully');
    }
}
