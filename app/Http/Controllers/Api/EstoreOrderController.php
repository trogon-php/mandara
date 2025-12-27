<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Estore\AppEstoreOrderResource;
use App\Services\App\EstoreService;
use Illuminate\Http\Request;

class EstoreOrderController extends BaseApiController
{
    public function __construct(protected EstoreService $estoreService) {}

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $user = $this->getAuthUser();
        $orders = $this->estoreService->getMyOrders($user->id, $perPage);
        $orders = AppEstoreOrderResource::collection($orders);

        return $this->respondPaginated($orders, 'Orders retrieved successfully');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'payment_method' => 'required|in:cod,online',
        ]);

        $user = $this->getAuthUser();
        $data['notes'] = [
            'email' => $user->email,
            'phone' => $user->country_code . $user->phone,
        ];
        $result = $this->estoreService->createOrder($user->id, $data);

        return $this->serviceResponse($result);
    }
    
    // Order complete and verify payment
    public function completeOrder(Request $request)
    {
        $data = $request->validate([
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $result = $this->estoreService->completeOrder($data);

        return $this->serviceResponse($result);
    }

    // public function show($id)
    // {
    //     $user = $this->getAuthUser();
    //     $result = $this->orderService->getOrderDetails($user->id, $id);

    //     if (!$result['status']) {
    //         return $this->respondError($result['message'], $result['http_code']);
    //     }

    //     $order = new AppEstoreOrderResource($result['data']);

    //     return $this->respondSuccess($order, 'Order details retrieved successfully');
    // }
}