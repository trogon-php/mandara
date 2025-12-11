<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\Orders\OrderService;
use App\Http\Requests\Orders\StoreOrderRequest as StoreRequest;
use App\Http\Requests\Orders\UpdateOrderRequest as UpdateRequest;

class OrderController extends AdminBaseController
{
    protected OrderService $service;

    public function __construct(OrderService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of orders
     */
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'user_id', 'package_id']);
        $searchParams = [
            'search' => $request->get('search'),
        ];

        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return !empty($value);
        });

        $params = [
            'search' => $searchParams['search'],
            'filters' => $filters,
        ];

        $list_items = $this->service->getFilteredData($params);
        // dd($this->service->getFilterConfig());
        return view('admin.orders.index', [
            'page_title' => 'Orders',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    /**
     * Show the form for creating a new order
     */
    public function create()
    {
        // $students = $this->service->getStudentsOptions();
        $packages = $this->service->getPackagesOptions();

        return view('admin.orders.create', [
            // 'users' => $students,
            'packages' => $packages,
        ]);
    }

    /**
     * Store a newly created order
     */
    public function store(StoreRequest $request)
    {
        $this->service->store($request->validated());
        return $this->successResponse('Order created successfully');
    }

    /**
     * Display the specified order
     */
    public function show($id)
    {
        $order = $this->service->find($id, ['user', 'package', 'coupon', 'userPayments']);
        
        if (!$order) {
            return $this->errorResponse('Order not found', 404);
        }

        return view('admin.orders.show', [
            'page_title' => 'Order Details',
            'order' => $order,
        ]);
    }

    /**
     * Show the form for editing the specified order
     */
    public function edit($id)
    {
        $order = $this->service->find($id, ['user', 'package', 'coupon']);
        $students = $this->service->getStudentsOptions();
        $packages = $this->service->getPackagesOptions();

        return view('admin.orders.edit', [
            'edit_data' => $order,
            'users' => $students,
            'packages' => $packages,
        ]);
    }

    /**
     * Update the specified order
     */
    public function update(UpdateRequest $request, $id)
    {
        $this->service->update($id, $request->validated());
        return $this->successResponse('Order updated successfully');
    }

    /**
     * Remove the specified order
     */
    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->successResponse('Order deleted successfully');
    }

    /**
     * Bulk delete orders
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:orders,id'
        ]);

        $deletedCount = $this->service->bulkDelete($request->ids);
        return $this->successResponse("{$deletedCount} orders deleted successfully");
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,partially_paid,paid,cancelled,refunded'
        ]);

        try {
            $updated = $this->service->updateStatus($id, $request->status);
            
            if (!$updated) {
                return $this->errorResponse('Order not found', 404);
            }

            return $this->successResponse('Order status updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update order status: ' . $e->getMessage());
        }
    }

    /**
     * Get order statistics
     */
    public function stats()
    {
        $stats = $this->service->getOrderStats();
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get orders by status
     */
    public function getByStatus(Request $request, $status)
    {
        $orders = $this->service->getOrdersByStatus($status);
        
        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Get order details for AJAX requests
     */
    public function getDetails($id)
    {
        $order = $this->service->find($id, ['user', 'package', 'coupon']);
        
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'user_id' => $order->user_id,
                'package_id' => $order->package_id,
                'amount_total' => $order->amount_total,
                'amount_final' => $order->amount_final,
                'status' => $order->status,
            ]
        ]);
    }

    /**
     * Get payments for a specific order
     */
    public function getPayments($id)
    {
        $order = $this->service->find($id, ['user', 'package', 'userPayments']);
        
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        $payments = $order->userPayments()->with('user')->get();

        return view('admin.orders.payments-modal', compact('order', 'payments'));
    }
}
