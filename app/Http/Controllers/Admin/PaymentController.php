<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\Payments\PaymentService;
use App\Http\Requests\Payments\StorePaymentRequest as StoreRequest;
use App\Http\Requests\Payments\UpdatePaymentRequest as UpdateRequest;

class PaymentController extends AdminBaseController
{
    protected PaymentService $service;

    public function __construct(PaymentService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of payments
     */
    public function index(Request $request)
    {
        $filters = $request->only(['payment_status', 'user_id', 'package_id', 'order_id']);
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

        return view('admin.payments.index', [
            'page_title' => 'Payments',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    /**
     * Show the form for creating a new payment
     */
    public function create()
    {
        $packages = $this->service->getPackagesOptions();
        $orders = $this->service->getOrdersOptions();

        return view('admin.payments.create', [
            'packages' => $packages,
            'orders' => $orders,
        ]);
    }

    /**
     * Store a newly created payment
     */
    public function store(StoreRequest $request)
    {
        $this->service->store($request->validated());
        return $this->successResponse('Payment created successfully');
    }

    /**
     * Display the specified payment
     */
    public function show($id)
    {
        $payment = $this->service->find($id, ['user', 'package', 'order', 'creator', 'updater']);
        
        if (!$payment) {
            return $this->errorResponse('Payment not found', 404);
        }

        return view('admin.payments.show', [
            'page_title' => 'Payment Details',
            'payment' => $payment,
        ]);
    }

    /**
     * Show the form for editing the specified payment
     */
    public function edit($id)
    {
        $payment = $this->service->find($id, ['user', 'package', 'order']);
        $packages = $this->service->getPackagesOptions();
        $orders = $this->service->getOrdersOptions();

        return view('admin.payments.edit', [
            'edit_data' => $payment,
            'packages' => $packages,
            'orders' => $orders,
        ]);
    }

    /**
     * Update the specified payment
     */
    public function update(UpdateRequest $request, $id)
    {
        $this->service->update($id, $request->validated());
        return $this->successResponse('Payment updated successfully');
    }

    /**
     * Remove the specified payment
     */
    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->successResponse('Payment deleted successfully');
    }

    /**
     * Bulk delete payments
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:user_payments,id'
        ]);

        $deletedCount = $this->service->bulkDelete($request->ids);
        return $this->successResponse("{$deletedCount} payments deleted successfully");
    }

    /**
     * Update payment status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded'
        ]);

        try {
            $updated = $this->service->updateStatus($id, $request->payment_status);
            
            if (!$updated) {
                return $this->errorResponse('Payment not found', 404);
            }

            return $this->successResponse('Payment status updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update payment status: ' . $e->getMessage());
        }
    }

    /**
     * Get payment statistics
     */
    public function stats()
    {
        $stats = $this->service->getPaymentStats();
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get payments by status
     */
    public function getByStatus(Request $request, $status)
    {
        $payments = $this->service->getPaymentsByStatus($status);
        
        return response()->json([
            'success' => true,
            'data' => $payments
        ]);
    }
}
