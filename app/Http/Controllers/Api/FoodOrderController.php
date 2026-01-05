<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Food\AppFoodOrderResource;
use App\Services\App\FoodService;
use Illuminate\Http\Request;

class FoodOrderController extends BaseApiController
{
    public function __construct(protected FoodService $foodService) {}

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $user = $this->getAuthUser();
        $orders = $this->foodService->getMyOrders($user->id, $perPage);
        // dd($orders);
        $orders = AppFoodOrderResource::collection($orders);

        return $this->respondPaginated($orders, 'Orders retrieved successfully');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'payment_method' => 'required|in:cod,online',
            'delivery_room' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $user = $this->getAuthUser();
        $data['notes'] = [
            'email' => $user->email,
            'phone' => $user->country_code . $user->phone,
            'delivery_room' => $data['delivery_room'] ?? null,
            'notes' => $data['notes'] ?? null,
        ];
        $result = $this->foodService->createOrder($user->id, $data);

        return $this->serviceResponse($result);
    }
    
    public function completeOrder(Request $request)
    {
        $data = $request->validate([
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $result = $this->foodService->completeOrder($data);

        return $this->serviceResponse($result);
    }
}