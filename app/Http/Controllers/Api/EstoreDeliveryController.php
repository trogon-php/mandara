<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Estore\DeliveryOrderResource;
use App\Services\App\EstoreDeliveryService;
use Illuminate\Http\Request;

class EstoreDeliveryController extends BaseApiController
{
    public function __construct(protected EstoreDeliveryService $deliveryService) {}

    /**
     * Get dashboard data
     */
    public function dashboard(Request $request)
    {
        $user = $this->getUserOrFail();
        
        if (is_object($user) && property_exists($user, 'status') && $user->status === 'blocked') {
            return $this->respondForbidden('Your account is blocked');
        }

        $result = $this->deliveryService->getDashboard($user->id);
        return $this->serviceResponse($result);
    }

    /**
     * Get orders list (pending or delivered)
     */
    public function index(Request $request)
    {
        $user = $this->getUserOrFail();
        
        if (is_object($user) && property_exists($user, 'status') && $user->status === 'blocked') {
            return $this->respondForbidden('Your account is blocked');
        }

        $status = $request->get('status', 'pending'); // pending or delivered
        $perPage = $request->get('per_page', 15);

        $result = $this->deliveryService->getOrders($user->id, $status, $perPage);
        
        if (!$result['status']) {
            return $this->respondError($result['message'], $result['http_code']);
        }

        $orders = DeliveryOrderResource::collection($result['data']);
        return $this->respondPaginated($orders, 'Orders retrieved successfully');
    }

    /**
     * Get order details
     */
    public function show(Request $request, string $id)
    {
        $user = $this->getUserOrFail();
        
        if (is_object($user) && property_exists($user, 'status') && $user->status === 'blocked') {
            return $this->respondForbidden('Your account is blocked');
        }

        $result = $this->deliveryService->getOrderDetails((int)$id, $user->id);
        
        if (!$result['status']) {
            return $this->respondError($result['message'], $result['http_code']);
        }

        $order = new DeliveryOrderResource($result['data']);
        return $this->respondSuccess($order, 'Order details retrieved successfully');
    }

    /**
     * Start delivery
     */
    public function startDelivery(Request $request, string $id)
    {
        $user = $this->getUserOrFail();
        
        if (is_object($user) && property_exists($user, 'status') && $user->status === 'blocked') {
            return $this->respondForbidden('Your account is blocked');
        }

        $result = $this->deliveryService->startDelivery((int)$id, $user->id);
        return $this->serviceResponse($result);
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, string $id)
    {
        $user = $this->getUserOrFail();
        
        if (is_object($user) && property_exists($user, 'status') && $user->status === 'blocked') {
            return $this->respondForbidden('Your account is blocked');
        }

        $data = $request->validate([
            'status' => 'required|in:assigned,accepted,out_for_delivery,delivered,failed',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $result = $this->deliveryService->updateOrderStatus(
            (int)$id,
            $user->id,
            $data['status'],
            $data['remarks'] ?? null
        );

        return $this->serviceResponse($result);
    }

    /**
     * Mark order as delivered
     */
    public function markDelivered(Request $request, string $id)
    {
        $user = $this->getUserOrFail();
        
        if (is_object($user) && property_exists($user, 'status') && $user->status === 'blocked') {
            return $this->respondForbidden('Your account is blocked');
        }

        $data = $request->validate([
            'remarks' => 'nullable|string|max:1000',
        ]);

        $result = $this->deliveryService->markDelivered(
            (int)$id,
            $user->id,
            $data['remarks'] ?? null
        );

        return $this->serviceResponse($result);
    }
}