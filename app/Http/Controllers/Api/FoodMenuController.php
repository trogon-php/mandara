<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Food\AppFoodMenuCollection;
use App\Services\App\FoodService;
use App\Services\Food\FoodMenuService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FoodMenuController extends BaseApiController
{
    public function __construct(
        protected FoodMenuService $foodMenuService,
        protected FoodService $foodService) {}

    public function index(Request $request)
    {
        $user = $this->getAuthUser();
        $isVeg = $user->getMetaField('is_veg');

        $foodMenuItems = $this->foodMenuService->getMenuByDate(Carbon::today()->format('Y-m-d'), $isVeg);
        $foodMenu = new AppFoodMenuCollection($foodMenuItems);
        
        $data = [
            'meal_configuration' => [
                'is_veg' => [
                    [
                        'value' => 0,
                        'label' => 'Non-Vegetarian',
                        'selected' => $isVeg == 0 ? true : false,
                    ],
                    [
                        'value' => 1,
                        'label' => 'Vegetarian',
                        'selected' => $isVeg == 1 ? true : false,
                    ]
                ],
                'deliver_breakfast_to_room' => (int) $user->getMetaField('deliver_breakfast_to_room'),
            ],
            'today_meals' => $foodMenu,
        ];

        return $this->respondSuccess($data, 'Food menu fetched successfully');
    }

    public function updateMealConfiguration(Request $request)
    {
        $data = $request->validate([
            'is_veg' => 'required|integer|in:0,1',
            'deliver_breakfast_to_room' => 'required|integer|in:0,1',
        ]);

        $user = $this->getAuthUser();
        $result = $this->foodService->updateMealConfiguration($user->id, $data);
        
        return $this->serviceResponse($result);
    }
}
