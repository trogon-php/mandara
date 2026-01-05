<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\MandaraBookings\MandaraBookingOrderService;
use App\Http\Requests\MandaraBookings\StoreMandaraBookingOrderRequest as StoreRequest;
use App\Http\Requests\MandaraBookings\UpdateMandaraBookingOrderRequest as UpdateRequest;

class MandaraPaymentController extends AdminBaseController
{
    protected MandaraBookingOrderService $service;

    public function __construct(MandaraBookingOrderService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of mandara bookingpayments
     */
    public function index(Request $request)
    {
       
        $filters = $request->only(['booking_id', 'payment_status','payment_order_id', 'payment_id']);
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

        return view('admin.mandara_payments.index', [
            'page_title' => 'Mandara Booking Payments',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
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

}
